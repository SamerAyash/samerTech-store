<?php

namespace App\Http\Resources;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $images = $this->images->map(function ($image) {
            $image->large  = $image->large ? asset('storage/' . $image->large) : null;
            $image->medium = $image->medium ? asset('storage/' . $image->medium) : null;
        
            return $image->only('large', 'medium', 'color', 'size', 'attributes', 'is_main');
        });
        
        $main_image= $images->where('is_main', 1)->first();
        $variants = $this->variants;
        $mainVariant = $variants->first();

        if ($main_image && $variants->isNotEmpty()) {
            $mainVariant = $variants->first(function ($variant) use ($main_image) {
                $attributes = $variant->attributes ?? [];
                return data_get($attributes, 'color') === ($main_image['color'] ?? null)
                    && data_get($attributes, 'size') === ($main_image['size'] ?? null);
            }) ?? $mainVariant;
        }
        
        // Get translation for the requested locale only
        $locale = get_api_locale($request);
        $translation = $this->translations->where('locale', $locale)->first();
        
        $data=[];
        $data['ref_code'] = $this->ref_code;
        $data['name'] = optional($translation)->name ?? '';
        $data['slug'] = arabic_slug(optional($translation)->name);
        $data['brand'] = optional($translation)->brand ?? '';
        $data['short_desc'] = optional($translation)->short_description ?? '';
        $data['description'] = optional($translation)->description ?? '';
        $data['details'] = optional($translation)->details ?? '';
        $data['images'] = $images;
        $data['categories'] = CategoryListResource::collection($this->categories->sortBy('sort_order'));
        
        $currencyService = app(CurrencyService::class);
        $data['price'] = $currencyService->convertFromQAR(optional($mainVariant)->price ?? 0, null, $request);
        $data['currency'] = $currencyService->getCurrencyCode($request);
        $data['variants'] = $variants->map(function($variant) use ($currencyService, $request) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'attributes' => $variant->attributes ?? [],
                'price' => $currencyService->convertFromQAR($variant->price ?? 0, null, $request),
                'qty' => $variant->stock ?? 0,
                'compare_at_price' => $variant->compare_at_price ?? null,
            ];
        })->toArray();
        return $data;
    }
}
