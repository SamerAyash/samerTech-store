<?php

namespace App\Http\Resources;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public $cartItem;
    public $product;

    public function __construct($product, $cartItem)
    {
        $this->product = $product;
        $this->cartItem = $cartItem;
    }
    public function toArray($request): array
    {
        $selectedImage = null;
        $selectedVariant = $this->product->selected_variant;
        $translation = $this->product->translations->first();
        $name = optional($translation)->name ?? null;
        $options = json_decode($this->cartItem->options, true) ?? [];
        $variantAttributes = $selectedVariant?->attributes ?? [];
        
        $images = $this->product->images()
            ->select(['id', 'product_id', 'small', 'color', 'size', 'attributes', 'is_main'])
            ->where(function ($query) use ($options) {
                $query->where('is_main', 1)
                    ->orWhere(function ($q) use ($options) {
                        $q->where('color', $options['color'] ?? null)
                            ->where('size', $options['size'] ?? null);
                    });
            })
            ->get();

        // المنطق: ابحث عن صورة تطابق اللون والحجم أولاً، إذا لم تجد خذ الرئيسية
        $selectedImage = $images->where('color', $options['color'] ?? null)
                                ->where('size', $options['size'] ?? null)
                                ->first() 
                        ?? $images->where('is_main', 1)->first();

        $currencyService = app(CurrencyService::class);
        
        return [
           'id' => $this->cartItem->id,
            'product_sku' => $this->product->ref_code,
            'variant_id' => $selectedVariant?->id,
            'name' => $name,
            'price' => $currencyService->convertFromQAR($selectedVariant?->price ?? 0, null, $request),
            'image' => $selectedImage ? asset('storage/' . $selectedImage->small) : null,
            'color' => key_exists('color', $options) ? $options['color'] : data_get($variantAttributes, 'color'),
            'size' => key_exists('size', $options) ? $options['size'] : data_get($variantAttributes, 'size'),
            'attributes' => $variantAttributes,
            'quantity' => $this->cartItem->quantity,
            'max_quantity' => (int) ($selectedVariant?->stock ?? 0),
        ];
    }
}