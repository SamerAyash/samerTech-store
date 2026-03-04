<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Post Service
 * 
 * Handles all post business logic including:
 * - Creating posts
 * - Updating posts
 * - Deleting posts
 * - Linking posts to products
 */
class PostService
{
    /**
     * Get latest published posts for homepage.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatestPosts(int $limit = 20)
    {
        return Post::published()
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all published posts with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPublishedPosts(int $perPage = 15)
    {
        return Post::published()
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get a single published post by slug.
     *
     * @param string $slug
     * @param string $locale
     * @return Post|null
     */
    public function getPostBySlug(string $slug): ?Post
    {
        return Post::published()
            ->whereHas('translations', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->with(['products' => function ($query) {
                $query->active()
                    ->with(['mainImage', 'firstVariant']);
            }])
            ->first();
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @param array|null $productIds
     * @return Post
     */
    public function createPost(array $data, ?array $productIds = null): Post
    {
        DB::beginTransaction();

        try {
            $post = Post::create($data);

            if ($productIds && !empty($productIds)) {
                $this->syncProducts($post, $productIds);
            }

            DB::commit();
            return $post;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing post.
     *
     * @param Post $post
     * @param array $data
     * @param array|null $productIds
     * @return Post
     */
    public function updatePost(Post $post, array $data, ?array $productIds = null): Post
    {
        DB::beginTransaction();

        try {
            $post->update($data);

            if ($productIds !== null) {
                $this->syncProducts($post, $productIds);
            }

            DB::commit();
            return $post->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a post.
     *
     * @param Post $post
     * @return bool
     */
    public function deletePost(Post $post): bool
    {
        DB::beginTransaction();

        try {
            $post->products()->detach();
            $deleted = $post->delete();

            DB::commit();
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Sync products to a post.
     *
     * @param Post $post
     * @param array $productIds
     * @return void
     */
    protected function syncProducts(Post $post, array $productIds): void
    {
        // Validate that all product IDs exist
        $existingProductIds = Product::whereIn('id', $productIds)->pluck('id')->toArray();
        $post->products()->sync($existingProductIds);
    }

}
