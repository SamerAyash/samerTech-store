<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class ProductSyncService
{
    public function syncByRefCode(string $refCode): array
    {
        return [
            'success' => false,
            'message' => 'Oracle sync was removed. Manage products and variants directly from admin.',
        ];
    }

    public function getVariationsByRefCode(string $refCode): array
    {
        $product = Product::query()->where('ref_code', $refCode)->first();
        if (!$product) {
            return [];
        }

        $variations = [];
        $variants = ProductVariant::query()
            ->where('product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($variants as $variant) {
            $attributes = $variant->attributes ?? [];
            $color = data_get($attributes, 'color');
            $size = data_get($attributes, 'size');
            $key = ($color ?? '') . '|' . ($size ?? '') . '|' . $variant->id;

            $variations[$key] = [
                'variant_id' => $variant->id,
                'color_desc' => $color,
                'color_code' => $color,
                'size_code' => $size,
                'price' => $variant->price ?? 0,
                'qty' => $variant->stock ?? 0,
                'attributes' => $attributes,
            ];
        }

        return $variations;
    }

    public function pullProductsFromOracle()
    {
        return [
            'success' => false,
            'message' => 'Oracle pull was removed from this project.',
        ];
    }
}

