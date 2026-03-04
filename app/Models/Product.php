<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Product extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $fillable = [
        'product_type_id',
        'ref_code',
        'status',
    ];
    public $translatedAttributes = ['name','brand','short_description','description','details'];
    public $translationModel = ProductTranslation::class;
    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    /**
     * Get product images relationship.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get main image.
     */
    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function firstVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get categories relationship (many-to-many).
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products', 'product_id', 'category_id')
            ->withTimestamps();
    }

    public function mainCategory(){
        $ParentCategory=  $this->belongsToMany(Category::class, 'category_products', 'product_id', 'category_id')
        ->whereNull("parent_id")->limit(1);
        if ($ParentCategory) return $ParentCategory;

        return $this->belongsToMany(Category::class, 'category_products', 'product_id', 'category_id')->limit(1);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    /**
     * Get home sliders relationship.
     */
    public function homeSliders()
    {
        return $this->hasMany(SliderProduct::class);
    }

    /**
     * Get home featured relationship.
     */
    public function homeFeatured()
    {
        return $this->hasMany(FeaturedProduct::class);
    }
}

