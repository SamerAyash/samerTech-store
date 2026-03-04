<?php

namespace App\Models;

use App\Traits\SlugGeneratorTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Category extends Model implements TranslatableContract
{
    use HasFactory, SlugGeneratorTrait, Translatable;
    protected $fillable = [
        'status',
        'parent_id',
        'sort_order',
        'active_navbar',
    ];

    protected $casts = [
        'status' => 'boolean',
        'active_navbar' => 'boolean',
    ];
    
    public $translatedAttributes = ['name','slug'];
    public $translationModel = CategoryTranslation::class;

    /**
     * Get products relationship.
     */
    public function category_products()
    {
        return $this->hasMany(CategoryProduct::class, 'caegory_id', 'product_id');
    }

    /**
     * Get parent category.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'id');
    }

    /**
     * Get child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    public static function tree()
    {
        return self::whereNull('parent_id')
            ->orderBy('sort_order')
            ->with(['children' => function ($q) {
                $q->orderBy('sort_order');
            }])->get();
    }
}
