<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllProductCardResource;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Category;
use App\Http\Resources\CategoryListResource;
use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductDetailsResource;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = get_api_locale($request);
        
        $queryParams = $request->all();
        ksort($queryParams);
        $cacheKey = 'products_v1_' . $locale . '_' . md5(json_encode($queryParams));

        $result = Cache::remember($cacheKey, 600, function () use ($request, $locale) {
            $categories = $this->parseArrayInput($request->categories);
            $typeCodes = $this->parseArrayInput($request->product_types);
            $sort = $request->get('sort');
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');

            $query = Product::active()
                ->with([
                    'images:id,product_id,medium,is_main,is_secondary',
                    'translations'=> function($query) use ($locale) {
                        $query->where('locale', $locale)
                        ->select(['id', 'product_id', 'locale', 'name']);
                    },
                    'variants:id,product_id,sku,price,stock,attributes,is_active,sort_order'
                ]);

            $allCategoryIds = $categories;
            $idsToCheck = $allCategoryIds;

            while (!empty($idsToCheck)) {
                $childIds = Category::whereIn('parent_id', $idsToCheck)->pluck('id')->toArray();
                if (empty($childIds)) {
                    break;
                }
                $allCategoryIds = array_merge($allCategoryIds, $childIds);
                $idsToCheck = $childIds;
            }
            
            $query->when(!empty($allCategoryIds), function ($q) use ($allCategoryIds) {
                $q->whereHas('categories', fn($subQ) =>
                 $subQ->whereHas('translations', fn($subqT) => 
                    $subqT->whereIn('slug', array_unique($allCategoryIds))));
            });

            if (!empty($typeCodes)) {
                $query->whereHas('productType', fn ($q) => $q->whereIn('code', $typeCodes));
            }

            $query->whereHas('variants', function ($q) use ($minPrice, $maxPrice) {
                $q->where('is_active', true);
                if ($minPrice !== null && $minPrice !== '') {
                    $q->where('price', '>=', (float) $minPrice);
                }
                if ($maxPrice !== null && $maxPrice !== '') {
                    $q->where('price', '<=', (float) $maxPrice);
                }
            });

            if ($sort === 'price-low' || $sort === 'price-high') {
                $query->withMin(['variants as min_variant_price' => function ($q) {
                    $q->where('is_active', true);
                }], 'price');
                $query->orderBy('min_variant_price', $sort === 'price-low' ? 'asc' : 'desc');
            } else {
                $query->orderByDesc('created_at');
            }

            $products = $query->paginate((int) min(max((int) $request->get('per_page', 15), 1), 100));

            return [
                'data' => AllProductCardResource::collection($products->items()),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem()
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'pagination' => $result['pagination'],
        ]);
    }
    private function parseArrayInput($input): array
    {
        if (!$input) return [];
        $array = is_array($input) ? $input : explode(',', $input);
        return array_filter(array_map('trim', $array));
    }

    public function getFilters(Request $request): JsonResponse
    {
        $locale = get_api_locale($request);

        $filters = Cache::remember('products_filters_' . $locale, 6400, function () use ($locale) {
            $categories = Category::active()
                ->select('id', 'sort_order', 'parent_id')
                ->with([
                    'translations' => function($query) use ($locale) {
                    $query->where('locale', $locale)
                    ->select(['id', 'category_id', 'locale', 'name','slug']);
                }])
                ->orderBy('sort_order')
                ->get();

            $productTypes = ProductType::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name']);

            return [
                'product_types' => $productTypes,
                'categories' => CategoryListResource::collection($categories),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $filters,
        ]);
    }
    public function show(Request $request, $refCodes): JsonResponse
    {
        $locale = get_api_locale($request);
        $contentSite= Cache::remember('content_site_'.$locale, 6400, function () use ($locale) {
            $shippingInformation = optional(SiteSetting::where('key', 'shipping_information_'.$locale)->first())->value;
            $returnsExchanges = optional(SiteSetting::where('key', 'returns_exchanges_'.$locale)->first())->value;
            $paymentOptionsSecurity = optional(SiteSetting::where('key', 'payment_options_security_'.$locale)->first())->value;
            return [
                'shippingInformation' => $shippingInformation,
                'returnsExchanges' => $returnsExchanges,
                'paymentOptionsSecurity' => $paymentOptionsSecurity,
            ];
        });

        $product = Product::active()
            ->with([
                'images:id,product_id,medium,large,color,size,is_main',
                'translations' => function($query) use ($locale) {
                    $query->where('locale', $locale)
                    ->select(['id', 'product_id', 'locale', 'name', 'brand', 'short_description', 'description', 'details']);
                },
                'categories' => function($query) use ($locale) {
                    $query->with(['translations' => function($subQuery) use ($locale) {
                        $subQuery->where('locale', $locale)
                        ->select(['id', 'category_id', 'locale', 'name']);
                    }]);
                },
                'variants:id,product_id,sku,price,stock,attributes,is_active,sort_order'
            ])
            ->where('ref_code', $refCodes)
            ->firstOrFail();
        $categoryIds = $product->categories->pluck('id');

        $secondaryProducts = Product::active()
            ->with([
                'mainImage:id,product_id,medium',
                'translations' => function($query) use ($locale) {
                    $query->where('locale', $locale)
                        ->select(['id', 'product_id', 'locale', 'name', 'short_description']);
                },
                'firstVariant:id,product_id,price'
            ])
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->withCount(['categories' => function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            }])
            ->where('id', '!=', $product->id)
            ->orderBy('categories_count', 'desc')
            ->take(15)
            ->get();

        $shuffled = $secondaryProducts->shuffle();
        $wearItWith = $shuffled->take(2);
        $recommendations = $shuffled->skip(2)->take(4);

        return response()->json([
            'product' =>  new ProductDetailsResource($product),
            'wearItWith' => ProductCardResource::collection($wearItWith),
            'recommendation' => ProductCardResource::collection($recommendations),
            'contentSite' => $contentSite
        ]);
    }
}
