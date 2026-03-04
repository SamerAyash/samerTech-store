<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostHomeResource;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    /**
     * Post service instance.
     *
     * @var PostService
     */
    protected PostService $postService;

    /**
     * Create a new controller instance.
     *
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Get latest posts for homepage.
     * 
     * Returns the latest 20 published posts with cache.
     * Cache is cleared automatically on post create/update/delete.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function home(Request $request): JsonResponse
    {
        $locale = get_api_locale($request);
        $cacheKey = 'posts_home_' . $locale;

        $posts = Cache::remember($cacheKey, 3600, function () {
            return $this->postService->getLatestPosts(20);
        });

        return response()->json([
            'success' => true,
            'data' => PostHomeResource::collection($posts),
        ]);
    }

    /**
     * Get all published posts with pagination.
     * 
     * Query parameters:
     * - page: Page number for pagination
     * - per_page: Items per page (default: 15, max: 100)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $locale = get_api_locale($request);
        $perPage = min((int) $request->get('per_page', 15), 100);
        
        $posts = $this->postService->getPublishedPosts($perPage);

        return response()->json([
            'success' => true,
            'data' => PostHomeResource::collection($posts->items()),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * Get a single published post by slug.
     * 
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $post = $this->postService->getPostBySlug($slug);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PostResource($post),
        ]);
    }
}
