<?php

namespace App\Models;

use App\Traits\SlugGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\Cache;

class Post extends Model implements TranslatableContract
{
    use HasFactory, SlugGeneratorTrait, Translatable;

    protected $fillable = [
        'featured_image',
        'status',
        'published_at',
    ];

    public $translatedAttributes = ['title', 'slug', 'content', 'excerpt', 'meta_title', 'meta_description'];
    public $translationModel = PostTranslation::class;

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache on create, update or delete
        static::created(function () {
            static::clearHomeCache();
        });

        static::updated(function () {
            static::clearHomeCache();
        });

        static::deleted(function () {
            static::clearHomeCache();
        });
    }

    /**
     * Clear homepage cache for all locales.
     */
    protected static function clearHomeCache(): void
    {
        try {
            Cache::forget('posts_home_en');
            Cache::forget('posts_home_ar');
        } catch (\Exception $e) {
            // Silently fail if cache clearing fails
        }
    }

    /**
     * Get products relationship (many-to-many).
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'post_product', 'post_id', 'product_id')
            ->withTimestamps();
    }

    /**
     * Scope to get only published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Get full URL for featured image.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    /**
     * Get SEO metadata as array.
     */
    public function getSeoMetadata($locale = 'en'): array
    {
        $translation = $this->translate($locale);
        
        return [
            'meta_title' => $translation->meta_title ?? $translation->title,
            'meta_description' => $translation->meta_description ?? $translation->excerpt,
            'og_title' => $translation->meta_title ?? $translation->title,
            'og_description' => $translation->meta_description ?? $translation->excerpt,
            'og_image' => $this->featured_image_url,
            'og_url' => url("/posts/{$translation->slug}"),
        ];
    }
}
