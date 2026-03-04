<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Get users data for DataTables (server-side processing).
     */
    public function data(Request $request)
    {
        $query = User::withCount('orders')
            ->orderByDesc('created_at');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                // Handle global search
                if ($request->has('search') && !empty($request->search['value'])) {
                    $keyword = $request->search['value'];
                    $query->where(function ($q) use ($keyword) {
                        $q->where('users.name', 'like', '%' . $keyword . '%')
                          ->orWhere('users.email', 'like', '%' . $keyword . '%')
                          ->orWhere('users.phone', 'like', '%' . $keyword . '%');
                    });
                }
                
                // Handle column-specific search
                if ($request->has('columns')) {
                    foreach ($request->columns as $column) {
                        if (isset($column['data']) && isset($column['search']['value']) && !empty($column['search']['value'])) {
                            $searchValue = $column['search']['value'];
                            
                            switch ($column['data']) {
                                case 'name':
                                    $query->where('users.name', 'like', '%' . $searchValue . '%');
                                    break;
                                case 'email':
                                    $query->where('users.email', 'like', '%' . $searchValue . '%');
                                    break;
                                case 'status':
                                    $query->where('users.status', $searchValue);
                                    break;
                            }
                        }
                    }
                }
            })
            ->editColumn('id', function ($user) {
                return '<span class="text-dark font-weight-bold">#' . $user->id . '</span>';
            })
            ->editColumn('name', function ($user) {
                $avatar = $user->avatar 
                    ? '<div class="symbol symbol-40 symbol-sm flex-shrink-0">
                        <img src="' . asset('storage/' . $user->avatar) . '" alt="' . e($user->name) . '">
                    </div>'
                    : '<div class="symbol symbol-40 symbol-sm flex-shrink-0">
                        <div class="symbol-label font-size-h6 font-weight-bold bg-light-primary text-primary">
                            ' . strtoupper(substr($user->name, 0, 1)) . '
                        </div>
                    </div>';
                
                return '<div class="d-flex align-items-center">
                    ' . $avatar . '
                    <div class="ml-3">
                        <span class="text-dark font-weight-bold">' . e($user->name) . '</span>
                    </div>
                </div>';
            })
            ->editColumn('email', function ($user) {
                return '<span class="text-dark">' . e($user->email) . '</span>';
            })
            ->editColumn('phone', function ($user) {
                return $user->phone ? '<span class="text-dark">' . e($user->phone) . '</span>' : '<span class="text-muted">-</span>';
            })
            ->editColumn('status', function ($user) {
                $status = $user->status ?? 'active';
                $badges = [
                    'active' => '<span class="badge badge-success">Active</span>',
                    'blocked' => '<span class="badge badge-danger">Blocked</span>',
                ];
                return $badges[$status] ?? $badges['active'];
            })
            ->addColumn('email_verified', function ($user) {
                if ($user->email_verified_at) {
                    return '<span class="badge badge-success">
                        <i class="flaticon2-check-mark"></i> Verified
                    </span>';
                }
                return '<span class="badge badge-light">
                    <i class="flaticon2-cross"></i> Not Verified
                </span>';
            })
            ->addColumn('orders_count', function ($user) {
                return '<span class="badge badge-light-primary">' . ($user->orders_count ?? 0) . '</span>';
            })
            ->addColumn('total_spent', function ($user) {
                $total = number_format($user->total_spent ?? 0, 2);
                return '<span class="text-dark font-weight-bold">$' . $total . '</span>';
            })
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('M d, Y');
            })
            ->addColumn('last_login', function ($user) {
                if ($user->last_login_at) {
                    return '<span class="text-muted font-size-sm">' . $user->last_login_at->diffForHumans() . '</span>';
                }
                return '<span class="text-muted">Never</span>';
            })
            ->addColumn('actions', function ($user) {
                return view('admin.users._actions', compact('user'))->render();
            })
            ->rawColumns(['id', 'name', 'email', 'phone', 'status', 'email_verified', 'orders_count', 'total_spent', 'last_login', 'actions'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->loadCount('orders');
        $user->load('orders');
        
        // Calculate statistics
        $totalSpent = $user->orders()->sum('total_amount') ?? 0;
        $averageOrderValue = $user->orders_count > 0 
            ? ($totalSpent / $user->orders_count) 
            : 0;
        $lastPurchaseDate = $user->orders()->latest()->first()?->created_at;
        
        $latestOrders = $user->orders()
            ->latest()
            ->take(10)
            ->get();

        return view('admin.users.show', compact('user', 'totalSpent', 'averageOrderValue', 'lastPurchaseDate', 'latestOrders'));
    }

    /**
     * Update user status.
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => ['required', 'in:active,blocked'],
        ]);

        $user->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully.'
        ]);
    }

    /**
     * Verify user email manually.
     */
    public function verifyEmail(User $user)
    {
        $user->update(['email_verified_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.'
        ]);
    }

    /**
     * Verify user phone manually.
     */
    public function verifyPhone(User $user)
    {
        $user->update(['phone_verified_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Phone verified successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $userName = $user->name;
        $ordersCount = $user->orders()->count();

        // Delete user orders
        if ($ordersCount > 0) {
            $user->orders()->delete();
        }

        $user->delete();

        $message = 'User "' . $userName . '" deleted successfully.';
        if ($ordersCount > 0) {
            $message .= ' ' . $ordersCount . ' order(s) were also deleted.';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}

