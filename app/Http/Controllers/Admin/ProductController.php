<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\ProductType;
use App\Services\ProductSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Stevebauman\Purify\Facades\Purify;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    protected $syncService;

    public function __construct(ProductSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.products.index');
    }

    public function create()
    {
        $categories = Category::with('translations')->get();
        $productTypes = ProductType::where('is_active', true)->with('templateAttributes')->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'productTypes'));
    }

    /**
     * Get products data for DataTables (server-side processing).
     */
    public function data(Request $request)
    {
        $query = Product::withCount('images')
            ->orderByDesc('created_at');

        // Apply status filter
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        // Apply has images filter
        if ($request->filled('filter_has_images')) {
            if ($request->filter_has_images == '1') {
                $query->has('images');
            } else {
                $query->doesntHave('images');
            }
        }

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $keyword = $request->search['value'];
                    $query->where(function ($q) use ($keyword) {
                        $q->where('products.ref_code', 'like', '%' . $keyword . '%')
                          ->orWhereTranslationLike('name', '%'.$keyword.'%');
                    });
                }
            })
            ->editColumn('ref_code', function ($product) {
                return '<span class="text-dark font-weight-bold">' . e($product->ref_code) . '</span>';
            })
            ->addColumn('name', function ($product) {
                return '<div class="d-flex flex-column">
                    <span class="text-dark font-weight-bold">' . e($product->translate('en')->name) . '</span>
                </div>';
            })
            ->editColumn('status', function ($product) {
                $badge = $product->status 
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
                return $badge;
            })
            ->addColumn('images_count', function ($product) {
                return '<span class="badge badge-light-info">' . $product->images_count . '</span>';
            })
            ->addColumn('last_synced', function ($product) {
                if ($product->last_synced_at) {
                    return '<span class="text-muted font-size-sm">' . $product->last_synced_at->diffForHumans() . '</span>';
                }
                return '<span class="text-muted">Never</span>';
            })
            ->addColumn('actions', function ($product) {
                return view('admin.products._actions', compact('product'))->render();
            })
            ->rawColumns(['ref_code', 'name', 'status', 'images_count', 'last_synced', 'actions'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $refCode)
    {
        $product = Product::where('ref_code', $refCode)
            ->with(['images', 'categories'])
            ->firstOrFail();
        $variations = $this->syncService->getVariationsByRefCode($refCode);
        
        return view('admin.products.show', compact('product',  'variations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $refCode)
    {
        $product = Product::where('ref_code', $refCode)
            ->with(['images', 'categories'])
            ->firstOrFail();
        $variations = $this->syncService->getVariationsByRefCode($refCode);
        
        // Get all categories ordered by name for multiselect
        $categories = Category::with('translations')
            ->orderByRaw('(SELECT name FROM category_translations WHERE category_translations.category_id = categories.id AND locale = "en" LIMIT 1)')
            ->get();

        return view('admin.products.edit', compact('product', 'variations', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_code' => ['required', 'string', 'max:255', 'unique:products,ref_code'],
            'en_name' => ['required', 'string', 'max:255'],
            'ar_name' => ['required', 'string', 'max:255'],
            'en_brand' => ['nullable', 'string', 'max:255'],
            'ar_brand' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
            'product_type_id' => ['nullable', 'exists:product_types,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.sku' => ['nullable', 'string', 'max:255', 'distinct'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.attributes' => ['nullable'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'variants.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = Product::create([
            'ref_code' => $request->ref_code,
            'product_type_id' => $request->product_type_id,
            'status' => (bool) $request->status,
            'en' => [
                'name' => $request->en_name,
                'brand' => $request->en_brand,
            ],
            'ar' => [
                'name' => $request->ar_name,
                'brand' => $request->ar_brand,
            ],
        ]);

        $product->categories()->sync($request->categories ?? []);

        foreach ($request->variants as $variantData) {
            $product->variants()->create([
                'sku' => $variantData['sku'] ?? null,
                'price' => $variantData['price'],
                'stock' => $variantData['stock'],
                'attributes' => $this->parseVariantAttributes($variantData['attributes'] ?? []),
                'is_active' => $variantData['is_active'] ?? true,
                'sort_order' => $variantData['sort_order'] ?? 0,
            ]);
        }

        Cache::flush();

        return redirect()->route('admin.products.edit', $product->ref_code)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $refCode)
    {
        $product = Product::where('ref_code', $refCode)->firstOrFail();

        $request->validate([
            'en_name' => ['required', 'string', 'max:255'],
            'ar_name' => ['required', 'string', 'max:255'],
            'en_brand' => ['nullable', 'string', 'max:255'],
            'ar_brand' => ['nullable', 'string', 'max:255'],
            'en_short_description' => ['nullable', 'string'],
            'ar_short_description' => ['nullable', 'string'],
            'en_description' => ['nullable', 'string'],
            'en_details' => ['nullable', 'string'],
            'ar_description' => ['nullable', 'string'],
            'ar_details' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
            'product_type_id' => ['nullable', 'exists:product_types,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'exists:product_variants,id'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.attributes' => ['nullable'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'variants.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $cleanDescription_en = Purify::clean($request->en_description);
        $cleanDetails_en = Purify::clean($request->en_details);
        $cleanDescription_ar = Purify::clean($request->ar_description);
        $cleanDetails_ar = Purify::clean($request->ar_details);

        // Update product
        $product->update([
            'en'=> [
                'name' => $request->en_name,
                'brand' => $request->en_brand,
                'short_description' => $request->en_short_description,
                'description' => $cleanDescription_en,
                'details' => $cleanDetails_en,
            ],
            'ar'=> [
                'name' => $request->ar_name,
                'brand' => $request->ar_brand,
                'short_description' => $request->ar_short_description,
                'description' => $cleanDescription_ar,
                'details' => $cleanDetails_ar,
            ],
            'status' => $request->status,
            'product_type_id' => $request->product_type_id,
        ]);

        // Sync categories (add/remove)
        $categoryIds = $request->categories ?? [];
        $product->categories()->sync($categoryIds);

        if ($request->has('variants')) {
            $payloadVariants = collect($request->variants);
            $existingIds = $payloadVariants->pluck('id')->filter()->map(fn ($id) => (int) $id)->values();
            $product->variants()->whereNotIn('id', $existingIds)->delete();

            foreach ($payloadVariants as $variantData) {
                $attributes = $this->parseVariantAttributes($variantData['attributes'] ?? []);
                $product->variants()->updateOrCreate(
                    ['id' => $variantData['id'] ?? null],
                    [
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'attributes' => $attributes,
                        'is_active' => $variantData['is_active'] ?? true,
                        'sort_order' => $variantData['sort_order'] ?? 0,
                    ]
                );
            }
        }

        // If request expects JSON (AJAX), return JSON response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $product->fresh()
            ]);
        }
		Cache::flush();
        return redirect()
            //->route('admin.products.show', $product->ref_code)
            ->back()
            ->with('success', 'Product updated successfully.');
    }

    private function parseVariantAttributes(mixed $attributes): array
    {
        if (is_array($attributes)) {
            return $attributes;
        }

        if (is_string($attributes) && trim($attributes) !== '') {
            $decoded = json_decode($attributes, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Upload product images (multiple).
     */
    public function uploadImage(Request $request, string $refCode)
    {
        $product = Product::where('ref_code', $refCode)->firstOrFail();

        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ]);

        $uploadedImages = [];
        foreach ($request->file('images') as $imageFile) {
            $imagePath = $this->storeImages($imageFile, $refCode, null, null);
            
            $image = $product->images()->create([
                'large' => $imagePath['large'],
                'medium' => $imagePath['medium'],
                'small' => $imagePath['small'],
                'is_main' => false,
                'color' => null,
                'size' => null,
            ]);

            $uploadedImages[] = [
                'id' => $image->id,
                'medium_url' => asset('storage/' . $imagePath['medium']),
                'large_url' => asset('storage/' . $imagePath['large']),
            ];
        }
        Cache::flush();
        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' image(s) uploaded successfully.',
            'images' => $uploadedImages,
            'gallery' => view('admin.products._product_image_gallery', ['product' => $product->fresh()])->render()
        ]);
    }

    /**
     * Update image attributes (color, size, is_main).
     */
    public function updateImageAttributes(Request $request, string $refCode, ProductImage $image)
    {
        $product = Product::where('ref_code', $refCode)->firstOrFail();
        
        // Verify image belongs to product
        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Image does not belong to this product.'
            ], 403);
        }

        $request->validate([
            'color' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'is_main' => ['nullable', 'boolean'],
            'is_secondary' => ['nullable', 'boolean'],
        ]);

        // If setting as main, unset other main images
        if ($request->has('is_main') && $request->is_main) {
            $product->images()->where('id', '!=', $image->id)->update(['is_main' => false]);
        }

        // If setting as secondary, unset other secondary images
        if ($request->has('is_secondary') && $request->is_secondary) {
            $product->images()->where('id', '!=', $image->id)->update(['is_secondary' => false]);
        }

        $updateData = [
            'is_main' => $request->has('is_main') ? $request->is_main : $image->is_main,
            'is_secondary' => $request->has('is_secondary') ? $request->is_secondary : $image->is_secondary,
        ];
        
        // Only update color if provided (can be empty string to clear)
        if ($request->has('color')) {
            $updateData['color'] = $request->color ?: null;
        }
        
        // Only update size if provided (can be empty string to clear)
        if ($request->has('size')) {
            $updateData['size'] = $request->size ?: null;
        }
        
        $image->update($updateData);
        Cache::flush();
        return response()->json([
            'success' => true,
            'message' => 'Image attributes updated successfully.',
            'gallery' => view('admin.products._product_image_gallery', ['product' => $product->fresh()])->render()
        ]);
    }

    /**
     * Get available colors and sizes for product.
     */
    public function getProductAttributes(string $refCode)
    {
        $product = Product::where('ref_code', $refCode)->firstOrFail();
        $typeAttributes = $product->productType?->templateAttributes()
            ->orderBy('sort_order')
            ->get(['id', 'code', 'name', 'input_type', 'is_required', 'is_variant_axis', 'options']);

        $variants = $product->variants()
            ->where('is_active', true)
            ->get(['id', 'sku', 'price', 'stock', 'attributes', 'sort_order']);

        return response()->json([
            'success' => true,
            'product_type' => $product->productType,
            'type_attributes' => $typeAttributes,
            'variants' => $variants,
        ]);
    }

    public function storeImages($imageInput, string $refCode,$color = null, $size = null)
    {
        // Directory structure - use 'general' if no color/size specified
        $colorPart = $color ? $color : 'general';
        $sizePart = $size ? $size : 'general';
        $baseDir = "products/{$refCode}/{$colorPart}_{$sizePart}";

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

        // ----------------------------
        // Save SMALL (max 300px on longest side)
        // ----------------------------
        $small = $manager->read($imageInput);
        $small->orient();
        $smallPath = "{$baseDir}/{$filenameBase}-small.webp";
        $small->scaleDown(width: 300, height: 300);
        $small->toWebp($quality)
            ->save(Storage::disk('public')->path($smallPath));

        // URLs
        return [
            'large'    => $largePath,
            'medium'   => $mediumPath,
            'small'    => $smallPath,
        ];
    }
    /**
     * Delete product image.
     */
    public function deleteImage(Request $request, string $refCode, ProductImage $image)
    {
        $product = Product::where('ref_code', $refCode)->firstOrFail();
        
        // Verify image belongs to product
        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Image does not belong to this product.'
            ], 403);
        }

        // Delete all image sizes from storage
        if ($image->large && Storage::disk('public')->exists($image->large)) {
            Storage::disk('public')->delete($image->large);
        }
        if ($image->medium && Storage::disk('public')->exists($image->medium)) {
            Storage::disk('public')->delete($image->medium);
        }
        if ($image->small && Storage::disk('public')->exists($image->small)) {
            Storage::disk('public')->delete($image->small);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.'
        ]);
    }

    /**
     * Set main image.
     */
    public function setMainImage(Request $request, string $refCode, ProductImage $image)
    {
        $product = Product::where('ref_code', $refCode)->firstOrFail();
        
        // Verify image belongs to product
        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Image does not belong to this product.'
            ], 403);
        }

        // Unset all main images
        $product->images()->update(['is_main' => false]);
        // Set this as main
        $image->update(['is_main' => true]);
        Cache::flush();
        return response()->json([
            'success' => true,
            'message' => 'Main image updated successfully.',
            'image' => view('admin.products._product_image_gallery', ['product' => $product])->render()
        ]);
    }

    /**
     * Set secondary image.
     */
    public function setSecondaryImage(Request $request, string $refCode, ProductImage $image)
    {
        $product = Product::where('ref_code', $refCode)->firstOrFail();
        
        // Verify image belongs to product
        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Image does not belong to this product.'
            ], 403);
        }

        // Unset all secondary images
        $product->images()->update(['is_secondary' => false]);
        // Set this as secondary
        $image->update(['is_secondary' => true]);
        Cache::flush();
        return response()->json([
            'success' => true,
            'message' => 'Secondary image updated successfully.',
            'image' => view('admin.products._product_image_gallery', ['product' => $product])->render()
        ]);
    }
}

