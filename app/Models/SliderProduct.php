<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderProduct extends Model
{
    use HasFactory;
    protected $table = 'slider_products';
    protected $fillable = [
        'slider_type',
        'product_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that belongs to this slider.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for fall/winter slider.
     */
    public function scopeFallWinter($query)
    {
        return $query->where('slider_type', 'fall_winter');
    }

    /**
     * Scope for spring/summer slider.
     */
    public function scopeSpringSummer($query)
    {
        return $query->where('slider_type', 'spring_summer');
    }
}
