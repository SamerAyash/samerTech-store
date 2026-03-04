<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGuestOrderRequest;
use App\Mail\OrderCreated;
use App\Models\Order;
use App\Models\Product;
use App\Services\AdminOrderService;
use App\Services\ProductSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function create(AdminOrderService $adminOrderService): View
    {
        $currencies = AdminOrderService::allowedCurrencies();
        $shippingMethods = $adminOrderService->getShippingMethodsForCurrency('QAR');

        return view('admin.orders.create', compact('currencies', 'shippingMethods'));
    }

    public function store(StoreGuestOrderRequest $request, AdminOrderService $adminOrderService): RedirectResponse
    {
        $order = $adminOrderService->createGuestOrder($request->validated());

        // Send order confirmation email to customer
        try {
            $customerEmail = $order->guest_email;
            if ($customerEmail) {
                Mail::to($customerEmail)->send(new OrderCreated($order));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order created email', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
            // Notify admins about the email failure
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order ' . $order->order_number . ' created successfully.');
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');

        $products = Product::where('status', true)
            ->where(function ($q) use ($query) {
                $q->where('ref_code', 'like', '%' . $query . '%')
                    ->orWhereTranslationLike('name', '%' . $query . '%');
            })
            ->with(['translations', 'mainImage', 'firstVariant'])
            ->limit(20)
            ->get();

        $results = $products->map(function ($product) {
            return [
                'ref_code' => $product->ref_code,
                'name' => $product->translate('en')?->name ?? $product->translate('ar')?->name ?? 'N/A',
                'image' => $product->mainImage ? asset('storage/' . $product->mainImage->medium) : null,
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function getProductVariants(string $ref_code, ProductSyncService $productSyncService)
    {
        $variations = $productSyncService->getVariationsByRefCode($ref_code);
        $variants = array_values(array_map(function ($v) {
            return [
                'variant_id' => $v['variant_id'] ?? null,
                'color_code' => $v['color_code'] ?? null,
                'color_desc' => $v['color_desc'] ?? ($v['color_code'] ?? ''),
                'size_code' => $v['size_code'] ?? null,
                'price' => (float) ($v['price'] ?? 0),
                'qty' => (int) ($v['qty'] ?? 0),
                'attributes' => $v['attributes'] ?? [],
            ];
        }, $variations));
        return response()->json(['variants' => $variants]);
    }

    public function shippingMethods(Request $request, AdminOrderService $adminOrderService)
    {
        $currency = $request->get('currency', 'QAR');
        $methods = $adminOrderService->getShippingMethodsForCurrency($currency);

        return response()->json(['shipping_methods' => $methods]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.orders.index');
    }

    /**
     * Get orders data for DataTables (server-side processing).
     */
    public function data(Request $request)
    {
        $query = Order::with(['user', 'orderItems'])
            ->withCount('orderItems')
            ->orderByDesc('created_at');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $keyword = $request->search['value'];
                    $query->where(function ($q) use ($keyword) {
                        $q->where('orders.order_number', 'like', '%' . $keyword . '%')
                          ->orWhere('orders.guest_name', 'like', '%' . $keyword . '%')
                          ->orWhere('orders.guest_email', 'like', '%' . $keyword . '%')
                          ->orWhereHas('user', function ($userQuery) use ($keyword) {
                              $userQuery->where('name', 'like', '%' . $keyword . '%')
                                        ->orWhere('email', 'like', '%' . $keyword . '%');
                          });
                    });
                }
            })
            ->editColumn('id', function ($order) {
                return '<span class="text-dark font-weight-bold">#' . $order->id . '</span>';
            })
            ->editColumn('order_number', function ($order) {
                return '<span class="text-dark font-weight-bold">' . e($order->order_number) . '</span>';
            })
            ->addColumn('user_name', function ($order) {
                if ($order->user) {
                    return '<div class="d-flex flex-column">
                        <a href="' . route('admin.users.show', $order->user) . '" class="text-primary font-weight-bold">' . e($order->user->name) . '</a>
                        <span class="text-muted font-size-sm">' . e($order->user->email) . '</span>
                    </div>';
                } elseif ($order->guest_id) {
                    $guestName = $order->guest_name ?? 'Guest';
                    $guestEmail = $order->guest_email ?? '-';
                    return '<div class="d-flex flex-column">
                        <span class="text-primary font-weight-bold">
                            ' . e($guestName) . ' 
                            <span class="badge badge-secondary badge-sm">Guest</span>
                        </span>
                        <span class="text-muted font-size-sm">' . e($guestEmail) . '</span>
                    </div>';
                }
                return '<span class="text-muted">Guest</span>';
            })
            ->editColumn('total_amount', function ($order) {
                $currency = $order->currency ?? 'QAR';
                $formatted = number_format((float) $order->total, 2) . ' ' . e($currency);
                return '<span class="text-dark font-weight-bold">' . $formatted . '</span>';
            })
            ->editColumn('payment_status', function ($order) {
                $badges = [
                    'pending' => '<span class="badge badge-warning">Pending</span>',
                    'paid' => '<span class="badge badge-success">Paid</span>',
                    'failed' => '<span class="badge badge-danger">Failed</span>',
                    'refunded' => '<span class="badge badge-info">Refunded</span>',
                ];
                return $badges[$order->payment_status] ?? $badges['pending'];
            })
            ->editColumn('status', function ($order) {
                $badges = [
                    'pending' => '<span class="badge badge-warning">Pending</span>',
                    'processing' => '<span class="badge badge-info">Processing</span>',
                    'shipped' => '<span class="badge badge-primary">Shipped</span>',
                    'delivered' => '<span class="badge badge-success">Delivered</span>',
                    'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
                ];
                return $badges[$order->status] ?? $badges['pending'];
            })
            ->addColumn('items_count', function ($order) {
                return '<span class="badge badge-light-primary">' . ($order->order_items_count ?? 0) . '</span>';
            })
            ->editColumn('created_at', function ($order) {
                return $order->created_at->format('M d, Y H:i');
            })
            ->addColumn('actions', function ($order) {
                return view('admin.orders._actions', compact('order'))->render();
            })
            ->rawColumns(['id', 'order_number', 'user_name', 'total_amount', 'payment_status', 'status', 'items_count', 'created_at', 'actions'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product.mainImage', 'shippingAddress', 'billingAddress', 'discountCode']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,delivered,cancelled'],
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.'
        ]);
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => ['required', 'in:pending,paid,failed,refunded'],
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully.'
        ]);
    }
}

