<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSliderProductRequest;
use App\Models\SliderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class SliderProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'fall_winter');

        if (!in_array($type, ['fall_winter', 'spring_summer'])) {
            $type = 'fall_winter';
        }

        $title = $type === 'fall_winter' ? 'FALL / WINTER' : 'SPRING / SUMMER';

        return view('admin.slider-products.index', compact('type', 'title'));
    }

    /**
     * Get sliders data for DataTables (server-side processing).
     */
    public function data(Request $request)
    {
        $type = $request->get('type', 'fall_winter');

        if (!in_array($type, ['fall_winter', 'spring_summer'])) {
            $type = 'fall_winter';
        }

        $query = SliderProduct::with('product.mainImage')
            ->where('slider_type', $type)
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
            ->addColumn('product_ref_code', function ($slider) {
                return '<span class="text-dark font-weight-bold">' . e($slider->product->ref_code) . '</span>';
            })
            ->addColumn('product_name', function ($slider) {
                $name = $slider->product->translate('en')->name ?? 'N/A';
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e($name) . '</span>
                </div>';
            })
            ->addColumn('product_image', function ($slider) {
                $image = $slider->product->mainImage;
                if ($image && $image->medium) {
                    $url = asset('storage/' . $image->medium);
                    return '<img src="' . $url . '" alt="Product" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">';
                }
                return '<span class="text-muted">No image</span>';
            })
            ->addColumn('created_at', function ($slider) {
                return '<span class="text-muted font-size-sm">' . $slider->created_at->format('Y-m-d H:i') . '</span>';
            })
            ->addColumn('actions', function ($slider) {
                return view('admin.slider-products._actions', compact('slider'))->render();
            })
            ->rawColumns(['product_ref_code', 'product_name', 'product_image', 'created_at', 'actions'])
            ->make(true);
    }

    /**
     * Search products for autocomplete (excludes already added products).
     */
    public function searchProducts(Request $request)
    {
        $type = $request->get('type', 'fall_winter');
        $query = $request->get('q', '');

        if (!in_array($type, ['fall_winter', 'spring_summer'])) {
            $type = 'fall_winter';
        }

        // Get products that are not already in this slider
        $existingProductIds = SliderProduct::where('slider_type', $type)
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
    public function store(StoreSliderProductRequest $request)
    {
        SliderProduct::create($request->validated());
        foreach (['en', 'ar'] as $locale) {
            Cache::forget($request->slider_type . '_slider' . '_' . $locale);
        }
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to slider successfully.'
            ]);
        }

        return redirect()
            ->route('admin.slider-products.index', ['type' => $request->slider_type])
            ->with('success', 'Product added to slider successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sliderProduct= SliderProduct::findOrFail($id);
        $sliderType = $sliderProduct->slider_type;
        $sliderProduct->delete();
        foreach (['en', 'ar'] as $locale) {
            Cache::forget($sliderType . '_slider' . '_' . $locale);
        }
        return redirect()
            ->route('admin.slider-products.index', ['type' => $sliderType])
            ->with('success', 'Product removed from slider successfully.');
    }
}
