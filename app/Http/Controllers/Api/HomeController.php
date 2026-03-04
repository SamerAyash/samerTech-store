<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCardResource;
use App\Models\Product;
use App\Models\SliderProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Get Fall/Winter slider products.
     */
    public function getFallWinterSlider(Request $request): JsonResponse
    {
        $locale = get_api_locale($request);
        
        $products = Cache::remember('fall_winter_slider_' . $locale, 36000, function () use ($locale) {
            $sliders = SliderProduct::with([
                'product.mainImage',
                'product.translations'=> function($query) use ($locale) {
                    $query->where('locale', $locale)
                    ->select(['id', 'product_id', 'locale', 'name']);
                },
                'product.firstVariant:id,product_id,price'])
            ->where('slider_type', 'fall_winter')
            ->orderBy('created_at', 'desc')
            ->get();
            $products = $sliders->map(function($product){
                return $product->product->status == 1 ? $product->product : null;
            })->filter();
            return $products;
        });

        return response()->json([
            'success' => true,
            'data' => ProductCardResource::collection($products),
        ]);
    }

    /**
     * Get Spring/Summer slider products.
     */
    public function getSpringSummerSlider(Request $request): JsonResponse
    {
        $locale = get_api_locale($request);
        
        $products =  Cache::remember('spring_summer_slider_' . $locale, 3600, function () use ($locale) {
            $sliders = SliderProduct::with([
                'product.mainImage',
                'product.translations'=> function($query) use ($locale) {
                    $query->where('locale', $locale)
                    ->select(['id', 'product_id', 'locale', 'name']);
                },
                'product.firstVariant:id,product_id,price'])
            ->where('slider_type', 'spring_summer')
            ->orderBy('created_at', 'desc')
            ->get();
            $products = $sliders->map(function($product){
                return $product->product->status == 1 ? $product->product : null;
            })->filter();
            return $products;
        });
        
        return response()->json([
            'success' => true,
            'data' => ProductCardResource::collection($products),
        ]);
    }

    /**
     * Get Featured Section A products.
     */
    public function getFeaturedA(Request $request): JsonResponse
    {
        $locale = get_api_locale($request);
        $products = Cache::remember('featured_A_' . $locale, 3600, function () use ($locale) {
            return Product::with([
                'mainImage:id,product_id,large,medium,small',
                'translations'=>function($query) use ($locale) {
                    $query->where('locale', $locale)
                    ->select(['id', 'product_id', 'locale', 'name']);
                },
            ])
            ->whereIn('id', function ($q) {
                $q->select('product_id')
                ->from('featured_products')
                ->where('section', 'A')
                ->orderByDesc('id');
            })
            ->active()
            ->limit(4)
            ->get();
        });

        return response()->json([
            'success' => true,
            'data' => ProductCardResource::collection($products),
        ]);
    }

    /**
     * Get Featured Section B products.
     */
    public function getFeaturedB(Request $request): JsonResponse  
    {
        $locale = get_api_locale($request);
        
        $products = Cache::remember('featured_B_' . $locale, 3600, function () use ($locale) {
            return Product::with([
                'mainImage:id,product_id,large,medium,small',
                'translations'=>function($query) use ($locale) {
                    $query->where('locale', $locale)
                    ->select(['id', 'product_id', 'locale', 'name']);
                },
                'firstVariant:id,product_id,price'
            ])
            ->whereIn('id', function ($q) {
                $q->select('product_id')
                ->from('featured_products')
                ->where('section', 'A')
                ->orderByDesc('id');
            })
            ->active()
            ->limit(4)
            ->get();
        });

        return response()->json([
            'success' => true,
            'data' => ProductCardResource::collection($products),
        ]);        
    }
}
