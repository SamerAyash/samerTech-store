<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.posts.index');
    }

    /**
     * Get posts data for DataTables (server-side processing).
     */
    public function data(Request $request)
    {
        $query = Post::withCount('products')
            ->orderByDesc('created_at');

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                // Handle global search
                if ($request->has('search') && !empty($request->search['value'])) {
                    $keyword = $request->search['value'];
                    $query->where(function ($q) use ($keyword) {
                        $q->whereHas('translations', function ($t) use ($keyword) {
                            $t->where('title', 'like', "%$keyword%");
                        });
                    });
                }
            })
            ->addColumn('title', function ($post) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e(optional($post->translate('en'))->title) . '</span>
                    <span class="text-muted font-size-sm">Created ' . $post->created_at->diffForHumans() . '</span>
                </div>';
            })
            ->addColumn('title_ar', function ($post) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e(optional($post->translate('ar'))->title) . '</span>
                </div>';
            })
            ->addColumn('slug', function ($post) {
                return '<span class="text-muted font-size-sm">' . e(optional($post->translate('en'))->slug) . '</span>';
            })
            ->addColumn('slug_ar', function ($post) {
                return '<span class="text-muted font-size-sm">' . e(optional($post->translate('ar'))->slug) . '</span>';
            })
            ->addColumn('products_count', function ($post) {
                return '<span class="badge badge-light-primary">' . $post->products_count . '</span>';
            })
            ->editColumn('status', function ($post) {
                $badges = [
                    'draft' => '<span class="badge badge-secondary">Draft</span>',
                    'published' => '<span class="badge badge-success">Published</span>',
                ];
                return $badges[$post->status] ?? '<span class="badge badge-secondary">' . $post->status . '</span>';
            })
            ->editColumn('published_at', function ($post) {
                if ($post->published_at) {
                    return '<span class="text-muted font-size-sm">' . $post->published_at->format('M d, Y H:i') . '</span>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->editColumn('created_at', function ($post) {
                return $post->created_at->format('M d, Y');
            })
            ->addColumn('actions', function ($post) {
                return view('admin.posts._actions', compact('post'))->render();
            })
            ->rawColumns(['title', 'title_ar', 'slug', 'slug_ar', 'products_count', 'status', 'published_at', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::active()
            ->orderByTranslation('name', 'asc', 'en')
            ->get();

        return view('admin.posts.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'en_title' => ['required', 'string', 'max:255'],
            'ar_title' => ['required', 'string', 'max:255'],
            'en_content' => ['required', 'string'],
            'ar_content' => ['required', 'string'],
            'en_excerpt' => ['nullable', 'string', 'max:500'],
            'ar_excerpt' => ['nullable', 'string', 'max:500'],
            'en_slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('post_translations', 'slug')->where('locale', 'en')
            ],
            'ar_slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('post_translations', 'slug')->where('locale', 'ar')
            ],
            'en_meta_title' => ['nullable', 'string', 'max:255'],
            'ar_meta_title' => ['nullable', 'string', 'max:255'],
            'en_meta_description' => ['nullable', 'string', 'max:500'],
            'ar_meta_description' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
        ]);

        DB::beginTransaction();

        try {
            // Calculate slugs
            $slug_en = $request->en_slug ?: Post::generateUniqueSlug('en', $request->en_title);
            $slug_ar = $request->ar_slug ?: Post::generateUniqueSlug('ar', $request->ar_title);

            // Create post first (without image)
            $post = Post::create([
                'featured_image' => null,
                'status' => $request->status,
                'published_at' => $request->published_at ? now()->parse($request->published_at) : null,
                'en' => [
                    'title' => $request->en_title,
                    'slug' => $slug_en,
                    'content' => $request->en_content,
                    'excerpt' => $request->en_excerpt,
                    'meta_title' => $request->en_meta_title,
                    'meta_description' => $request->en_meta_description,
                ],
                'ar' => [
                    'title' => $request->ar_title,
                    'slug' => $slug_ar,
                    'content' => $request->ar_content,
                    'excerpt' => $request->ar_excerpt,
                    'meta_title' => $request->ar_meta_title,
                    'meta_description' => $request->ar_meta_description,
                ],
            ]);

            // Handle featured image upload directly to post directory
            if ($request->hasFile('featured_image')) {
                $imagePaths = $this->storeFeaturedImage($request->file('featured_image'), $post->id);
                // Update post with medium size path as featured_image
                $post->update(['featured_image' => $imagePaths['medium']]);
            }

            // Sync products
            if ($request->has('products')) {
                $post->products()->sync($request->products);
            }

            DB::commit();
            foreach (['en', 'ar'] as $locale) {
                Cache::forget('posts_home_' . $locale);
            }

            return redirect()
                ->route('admin.posts.index')
                ->with('success', 'Post created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create post: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $products = Product::active()
            ->orderByTranslation('name', 'asc', 'en')
            ->get();

        $post->load('products');

        return view('admin.posts.edit', compact('post', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'en_title' => ['required', 'string', 'max:255'],
            'ar_title' => ['required', 'string', 'max:255'],
            'en_content' => ['required', 'string'],
            'ar_content' => ['required', 'string'],
            'en_excerpt' => ['nullable', 'string', 'max:500'],
            'ar_excerpt' => ['nullable', 'string', 'max:500'],
            'en_slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('post_translations', 'slug')
                    ->where('locale', 'en')
                    ->ignore(optional($post->translate('en'))->id, 'id')
            ],
            'ar_slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('post_translations', 'slug')
                    ->where('locale', 'ar')
                    ->ignore(optional($post->translate('ar'))->id, 'id')
            ],
            'en_meta_title' => ['nullable', 'string', 'max:255'],
            'ar_meta_title' => ['nullable', 'string', 'max:255'],
            'en_meta_description' => ['nullable', 'string', 'max:500'],
            'ar_meta_description' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
        ]);

        DB::beginTransaction();

        try {
            // Calculate slugs if title changed
            $slug_en = $request->en_slug;
            if (!$slug_en || $post->translate('en')->title !== $request->en_title) {
                $slug_en = $request->en_slug ?: Post::generateUniqueSlug('en', $request->en_title, $post->id);
            }

            $slug_ar = $request->ar_slug;
            if (!$slug_ar || $post->translate('ar')->title !== $request->ar_title) {
                $slug_ar = $request->ar_slug ?: Post::generateUniqueSlug('ar', $request->ar_title, $post->id);
            }

            // Handle featured image upload
            $featuredImage = $post->featured_image;
            if ($request->hasFile('featured_image')) {
                // Delete old images if exists
                if ($featuredImage) {
                    $this->deleteFeaturedImage($featuredImage);
                }
                $imagePaths = $this->storeFeaturedImage($request->file('featured_image'), $post->id);
                // Store medium size path as featured_image
                $featuredImage = $imagePaths['medium'];
            }

            $post->update([
                'featured_image' => $featuredImage,
                'status' => $request->status,
                'published_at' => $request->published_at ? now()->parse($request->published_at) : null,
                'en' => [
                    'title' => $request->en_title,
                    'slug' => $slug_en,
                    'content' => $request->en_content,
                    'excerpt' => $request->en_excerpt,
                    'meta_title' => $request->en_meta_title,
                    'meta_description' => $request->en_meta_description,
                ],
                'ar' => [
                    'title' => $request->ar_title,
                    'slug' => $slug_ar,
                    'content' => $request->ar_content,
                    'excerpt' => $request->ar_excerpt,
                    'meta_title' => $request->ar_meta_title,
                    'meta_description' => $request->ar_meta_description,
                ],
            ]);

            // Sync products
            $post->products()->sync($request->products ?? []);

            DB::commit();
            foreach (['en', 'ar'] as $locale) {
                Cache::forget('posts_home_' . $locale);
            }

            return redirect()
                ->route('admin.posts.index')
                ->with('success', 'Post updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update post: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            // Delete featured image if exists
            if ($post->featured_image) {
                $this->deleteFeaturedImage($post->featured_image);
            }

            $post->products()->detach();
            $post->delete();
            foreach (['en', 'ar'] as $locale) {
                Cache::forget('posts_home_' . $locale);
            }
            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search products for autocomplete.
     */
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        
        $products = Product::active()
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

    protected function storeFeaturedImage($imageInput, $postId)
    {
        // Directory structure: posts/{post_id}
        $baseDir = "posts/{$postId}";

        if (!Storage::disk('public')->exists($baseDir)) {
            Storage::disk('public')->makeDirectory($baseDir);
        }

        // Generate unique base name
        $filenameBase = Str::uuid()->toString();
        $quality = 95; // جودة webp عالية للحفاظ على أفضل جودة ممكنة
        $manager = new ImageManager(new Driver());

        // ----------------------------
        // Save LARGE (max 1200px on longest side)
        // نقرأ الصورة من جديد لكل حجم لتفادي مشاكل clone مع GD
        // ----------------------------
        $large = $manager->read($imageInput);
        $large->orient(); // تصحيح اتجاه الصورة حسب بيانات EXIF
        $largePath = "{$baseDir}/{$filenameBase}-large.webp";
        $large->scaleDown(width: 1200, height: 1200);
        $large->toWebp($quality)
            ->save(Storage::disk('public')->path($largePath));

        // ----------------------------
        // Save MEDIUM (max 600px on longest side)
        // ----------------------------
        $medium = $manager->read($imageInput);
        $medium->orient();
        $mediumPath = "{$baseDir}/{$filenameBase}-medium.webp";
        $medium->scaleDown(width: 600, height: 600);
        $medium->toWebp($quality)
            ->save(Storage::disk('public')->path($mediumPath));

        return [
            'large' => $largePath,
            'medium' => $mediumPath,
        ];
    }

    /**
     * Delete featured image and all its sizes.
     */
    protected function deleteFeaturedImage($imagePath)
    {
        // Extract base directory and filename from medium path
        // Format: posts/{post_id}/{uuid}-medium.webp
        if (preg_match('/^(posts\/[^\/]+\/[^\/]+)-medium\.webp$/', $imagePath, $matches)) {
            $basePath = $matches[1];
            
            // Delete all sizes
            $sizes = ['large', 'medium'];
            foreach ($sizes as $size) {
                $sizePath = "{$basePath}-{$size}.webp";
                if (Storage::disk('public')->exists($sizePath)) {
                    Storage::disk('public')->delete($sizePath);
                }
            }
        } else {
            // Fallback: delete the single file if it doesn't match the pattern
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
    }

}
