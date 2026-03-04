<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DiscountCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.discount-codes.index');
    }

    /**
     * Get discount codes data for DataTables (server-side processing).
     */
    public function data(Request $request)
    {
        $query = DiscountCode::withCount('orders')
            ->orderByDesc('created_at');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $keyword = $request->search['value'];
                    $query->where(function ($q) use ($keyword) {
                        $q->where('code', 'like', '%' . $keyword . '%')
                          ->orWhere('name', 'like', '%' . $keyword . '%')
                          ->orWhere('description', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->editColumn('id', function ($code) {
                return '<span class="text-dark font-weight-bold">#' . $code->id . '</span>';
            })
            ->editColumn('code', function ($code) {
                return '<span class="text-dark font-weight-bold">' . e($code->code) . '</span>';
            })
            ->editColumn('name', function ($code) {
                return $code->name ? e($code->name) : '<span class="text-muted">-</span>';
            })
            ->editColumn('discount_type', function ($code) {
                $badges = [
                    'percentage' => '<span class="badge badge-info">Percentage</span>',
                    'fixed' => '<span class="badge badge-primary">Fixed</span>',
                ];
                return $badges[$code->discount_type] ?? '-';
            })
            ->editColumn('discount_value', function ($code) {
                if ($code->discount_type === 'percentage') {
                    return '<span class="text-dark font-weight-bold">' . number_format($code->discount_value, 2) . '%</span>';
                }
                return '<span class="text-dark font-weight-bold">$' . number_format($code->discount_value, 2) . '</span>';
            })
            ->editColumn('usage_limit', function ($code) {
                if ($code->usage_limit) {
                    return '<span class="text-dark">' . $code->used_count . ' / ' . $code->usage_limit . '</span>';
                }
                return '<span class="text-muted">Unlimited</span>';
            })
            ->editColumn('status', function ($code) {
                $isValid = $code->isValid();
                $statusBadge = $code->status 
                    ? '<span class="badge badge-success">Active</span>' 
                    : '<span class="badge badge-danger">Inactive</span>';
                $validBadge = $isValid 
                    ? '<span class="badge badge-info">Valid</span>' 
                    : '<span class="badge badge-warning">Invalid</span>';
                return $statusBadge . ' ' . $validBadge;
            })
            ->editColumn('start_date', function ($code) {
                return $code->start_date ? $code->start_date->format('M d, Y') : '<span class="text-muted">-</span>';
            })
            ->editColumn('end_date', function ($code) {
                return $code->end_date ? $code->end_date->format('M d, Y') : '<span class="text-muted">-</span>';
            })
            ->editColumn('created_at', function ($code) {
                return $code->created_at->format('M d, Y H:i');
            })
            ->addColumn('actions', function ($code) {
                return view('admin.discount-codes._actions', compact('code'))->render();
            })
            ->rawColumns(['id', 'code', 'name', 'discount_type', 'discount_value', 'usage_limit', 'status', 'start_date', 'end_date', 'created_at', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.discount-codes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:discount_codes,code'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['boolean'],
        ]);

        if ($request->has('max_discount') && $request->discount_type === 'fixed') {
            unset($validated['max_discount']);
        }

        $validated['status'] = $request->has('status') ? true : false;
        $validated['used_count'] = 0;

        if ($request->has('start_date') && $request->start_date) {
            $validated['start_date'] = Carbon::parse($request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $validated['end_date'] = Carbon::parse($request->end_date);
        }

        DiscountCode::create($validated);

        return redirect()->route('admin.discount-codes.index')
            ->with('success', 'Discount code created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DiscountCode $discountCode)
    {
        $discountCode->load('orders.user');
        return view('admin.discount-codes.show', compact('discountCode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DiscountCode $discountCode)
    {
        return view('admin.discount-codes.edit', compact('discountCode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiscountCode $discountCode)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:discount_codes,code,' . $discountCode->id],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['boolean'],
        ]);

        if ($request->has('max_discount') && $request->discount_type === 'fixed') {
            unset($validated['max_discount']);
        } else {
            $validated['max_discount'] = $request->max_discount ?? null;
        }

        $validated['status'] = $request->has('status') ? true : false;

        if ($request->has('start_date') && $request->start_date) {
            $validated['start_date'] = Carbon::parse($request->start_date);
        } else {
            $validated['start_date'] = null;
        }

        if ($request->has('end_date') && $request->end_date) {
            $validated['end_date'] = Carbon::parse($request->end_date);
        } else {
            $validated['end_date'] = null;
        }

        $discountCode->update($validated);

        return redirect()->route('admin.discount-codes.index')
            ->with('success', 'Discount code updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiscountCode $discountCode)
    {
        if ($discountCode->orders()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete discount code that has been used in orders.'
            ], 422);
        }

        $discountCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Discount code deleted successfully.'
        ]);
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(DiscountCode $discountCode)
    {
        $discountCode->update(['status' => !$discountCode->status]);

        return response()->json([
            'success' => true,
            'message' => 'Discount code status updated successfully.',
            'status' => $discountCode->status
        ]);
    }
}
