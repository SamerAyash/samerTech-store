<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Post;
use App\Models\Contact;
use App\Models\DiscountCode;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Currency service instance.
     *
     * @var CurrencyService
     */
    protected CurrencyService $currencyService;

    /**
     * Create a new controller instance.
     *
     * @param CurrencyService $currencyService
     */
    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Display the dashboard with statistics.
     */
    public function index()
    {
        // Users Statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $blockedUsers = User::where('status', 'blocked')->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Orders Statistics - Convert all to QAR
        $totalOrders = Order::count();
        // Order Status Breakdown
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // Payment Status Breakdown
        $paidOrders = Order::where('payment_status', 'paid')->count();
        $pendingPaymentOrders = Order::where('payment_status', 'pending')->count();
        $failedPaymentOrders = Order::where('payment_status', 'failed')->count();
        $refundedPaymentOrders = Order::where('payment_status', 'refunded')->count();

        // Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Products Statistics
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 1)->count();
        $inactiveProducts = Product::where('status', 0)->count();

        // Categories Statistics
        $totalCategories = Category::count();
        $activeCategories = Category::where('status', 1)->count();

        // Posts Statistics
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $draftPosts = Post::where('status', 'draft')->count();

        // Contact Messages Statistics
        $totalMessages = Contact::count();
        $unreadMessages = Contact::where('readed', false)->count();
        $readMessages = Contact::where('readed', true)->count();

        // Discount Codes Statistics
        $totalDiscountCodes = DiscountCode::count();
        $activeDiscountCodes = DiscountCode::where('status', true)->count();

        // Orders by Month (Last 6 months) - Convert all to QAR
        $ordersByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthOrders = Order::where('payment_status', 'paid')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->get();
            
            $monthRevenueQAR = 0;
            foreach ($monthOrders as $order) {
                $orderTotal = (float) ($order->total ?? 0);
                $monthRevenueQAR += $this->currencyService->convertToQAR($orderTotal, $order->currency ?? 'QAR');
            }
            
            $ordersByMonth[] = [
                'month' => $date->format('M Y'),
                'count' => Order::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'revenue' => round($monthRevenueQAR, 2),
            ];
        }

        // Top Products (by order items)
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_sku', '=', 'products.ref_code')
            ->select('products.ref_code', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.ref_code')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'blockedUsers',
            'newUsersToday',
            'newUsersThisMonth',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders',
            'paidOrders',
            'pendingPaymentOrders',
            'failedPaymentOrders',
            'refundedPaymentOrders',
            'recentOrders',
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'totalCategories',
            'activeCategories',
            'totalPosts',
            'publishedPosts',
            'draftPosts',
            'totalMessages',
            'unreadMessages',
            'readMessages',
            'totalDiscountCodes',
            'activeDiscountCodes',
            'ordersByMonth',
            'topProducts'
        ));
    }
}
