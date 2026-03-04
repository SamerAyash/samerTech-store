<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'product_sku',
        'product_variant_id',
        'product_name',
        'price',
        'quantity',
        'color',
        'size',
        'attributes',
        'image',
        'subtotal',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
        'attributes' => 'array',
    ];

    /**
     * Get order relationship.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get product relationship (order_items.product_sku -> products.ref_code).
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_sku', 'ref_code');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
