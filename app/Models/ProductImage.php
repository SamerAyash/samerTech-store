<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'large',
        'medium',
        'small',
        'color',
        'size',
        'attributes',
        'is_main',
        'is_secondary',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_secondary' => 'boolean',
        'attributes' => 'array',
    ];

    /**
     * Get product relationship.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
