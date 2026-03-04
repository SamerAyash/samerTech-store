<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function getCategories(Request $request): JsonResponse
    {
        try {
            $locale = get_api_locale($request);
            
            $categories = Category::where('status', true)
                ->orderByRaw('COALESCE(sort_order, id)')
                ->with([
                    'translations' => function($query) use ($locale) {
                        $query->where('locale', $locale)
                            ->select(['id', 'category_id', 'locale', 'name','slug']);
                    }
                ])
                ->get()
                ->map(function ($category) use ($locale) {
                    $translation = $category->translations->where('locale', $locale)->first();
                    
                    return [
                        'id' => $category->id,
                        'name' => optional($translation)->name ?? '',
                        'slug' =>optional($translation)->slug ?? '',
                        'active_navbar' => $category->active_navbar,
                        'order' => $category->sort_order ?? $category->id,
                    ];
                });
            $navbarCategories = $categories
                ->filter(function ($category) {
                    return $category['active_navbar'] === true;
                })
                ->values();
            return response()->json([
                'data' => ["allCategories" => $categories, "navCategories" => $navbarCategories],
                'success' => true
            ]);
        } 
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching navigation categories.'
            ], 500);
        }
    }
}
