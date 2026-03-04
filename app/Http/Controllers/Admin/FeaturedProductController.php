<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeaturedProductRequest;
use App\Models\FeaturedProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class FeaturedProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $section = $request->get('section', 'A');

        if (!in_array($section, ['A', 'B'])) {
            $section = 'A';
        }

        $title = 'Featured Section ' . $section;
        $currentCount = FeaturedProduct::where('section', $section)->count();

        return view('admin.featured-products.index', compact('section', 'title', 'currentCount'));
    }

    /**
     * Get featured products data for DataTables (server-side processing).
     */
    public function data(Request $request)
    {
        $section = $request->get('section', 'A');

        if (!in_array($section, ['A', 'B'])) {
            $section = 'A';
        }

        $query = FeaturedProduct::with('product.mainImage')
            ->where('section', $section)
            ->orderByDesc('created_at');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $keyword = $request->search['value'];
                    $query->whereHas('product', function ($q) use ($keyword) {
                        $q->where('ref_code', 'like', '%' . $keyword . '%')
                          ->orWhereTranslationLike('name', '%' . $keyword . '%');
                    });
                }
            })
            ->addColumn('product_ref_code', function ($featured) {
                return '<span class="text-dark font-weight-bold">' . e($featured->product->ref_code) . '</span>';
            })
            ->addColumn('product_name', function ($featured) {
                $name = $featured->product->translate('en')->name ?? 'N/A';
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e($name) . '</span>
                </div>';
            })
            ->addColumn('product_image', function ($featured) {
                $image = $featured->product->mainImage;
                if ($image && $image->medium) {
                    $url = asset('storage/' . $image->medium);
                    return '<img src="' . $url . '" alt="Product" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">';
                }
                return '<span class="text-muted">No image</span>';
            })
            ->addColumn('created_at', function ($featured) {
                return '<span class="text-muted font-size-sm">' . $featured->created_at->format('Y-m-d H:i') . '</span>';
            })
            ->addColumn('actions', function ($featured) {
                return view('admin.featured-products._actions', compact('featured'))->render();
            })
            ->rawColumns(['product_ref_code', 'product_name', 'product_image', 'created_at', 'actions'])
            ->make(true);
    }

    /**
     * Search products for autocomplete (excludes already added products).
     */
    public function searchProducts(Request $request)
    {
        $section = $request->get('section', 'A');
        $query = $request->get('q', '');

        if (!in_array($section, ['A', 'B'])) {
            $section = 'A';
        }

        // Check if section already has 4 products
        $count = FeaturedProduct::where('section', $section)->count();
        if ($count >= 4) {
            return response()->json([
                'results' => [],
                'error' => 'Featured section can only have a maximum of 4 products.'
            ]);
        }

        // Get products that are not already in this section
        $existingProductIds = FeaturedProduct::where('section', $section)
            ->pluck('product_id')
            ->toArray();

        $products = Product::whereNotIn('id', $existingProductIds)
            ->where('status', true)
            ->where(function ($q) use ($query) {
                $q->where('ref_code', 'like', '%' . $query . '%')
                  ->orWhereTranslationLike('name', '%' . $query . '%');
            })
            ->with(['translations', 'mainImage'])
            ->limit(20)
            ->get();

        $results = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'text' => $product->ref_code . ' - ' . ($product->translate('en')->name ?? 'N/A'),
                'ref_code' => $product->ref_code,
                'name_en' => $product->translate('en')->name ?? null,
                'name_ar' => $product->translate('ar')->name ?? null,
                'image' => $product->mainImage ? asset('storage/' . $product->mainImage->medium) : null,
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeaturedProductRequest $request)
    {
        // Check if section already has 4 products
        $count = FeaturedProduct::where('section', $request->section)->count();

        if ($count >= 4) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Featured section can only have a maximum of 4 products.'
                ], 400);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Featured section can only have a maximum of 4 products.');
        }

        FeaturedProduct::create($request->validated());

        foreach (['en', 'ar'] as $locale) {
            Cache::forget('featured_' . strtoupper($request->section) . '_' . $locale);
        }
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to featured section successfully.'
            ]);
        }

        return redirect()
            ->route('admin.featured-products.index', ['section' => $request->section])
            ->with('success', 'Product added to featured section successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $featuredProduct= FeaturedProduct::findOrFail($id);
        $section = $featuredProduct->section;
        $featuredProduct->delete();
        foreach (['en', 'ar'] as $locale) {
            Cache::forget('featured_' . strtoupper($section) . '_' . $locale);
        }
        return redirect()
            ->route('admin.featured-products.index', ['section' => $section])
            ->with('success', 'Product removed from featured section successfully.');
    }
}
