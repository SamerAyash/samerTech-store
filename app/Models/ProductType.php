<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function templateAttributes()
    {
        return $this->hasMany(ProductTypeAttribute::class)->orderBy('sort_order');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
