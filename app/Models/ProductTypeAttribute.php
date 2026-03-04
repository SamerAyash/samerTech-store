<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTypeAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_type_id',
        'code',
        'name',
        'input_type',
        'is_required',
        'is_variant_axis',
        'options',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_variant_axis' => 'boolean',
        'options' => 'array',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}
