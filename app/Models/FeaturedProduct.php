<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'product_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that belongs to this featured section.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for section A.
     */
    public function scopeSectionA($query)
    {
        return $query->where('section', 'A');
    }

    /**
     * Scope for section B.
     */
    public function scopeSectionB($query)
    {
        return $query->where('section', 'B');
    }
}
