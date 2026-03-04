<?php

namespace App\Http\Resources;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllProductCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = $this->images;
        $mainImage = $images->where('is_main', true)->first() ?? $images->first();
        $secondaryImage = $images->where('is_secondary', true)->first();
        if(!$secondaryImage){
            $secondaryImage = $images->where('id', '!=', optional($mainImage)->id)->first();
        }
        
        $variants = $this->variants;
        $locale = get_api_locale($request);
        $translation = $this->translations->where('locale', $locale)->first();
        $name = optional($translation)->name ?? null;
        $short_description = optional($translation)->short_description ?? null;
        $currencyService = app(CurrencyService::class);
        $colorValues = $variants
            ->map(fn ($variant) => data_get($variant->attributes ?? [], 'color'))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $colorsWithoutStock = $variants
            ->filter(fn ($variant) => (int) $variant->stock < 1)
            ->map(fn ($variant) => data_get($variant->attributes ?? [], 'color'))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        return [
            'ref_code' => $this->ref_code,
            'name' => $name,
            'slug' => $name ? arabic_slug($name) : null,
            'main_image' => $mainImage &&  $mainImage->medium ? 
                 asset('storage/' . $mainImage->medium)
                : null,
            'secondary_image' => $secondaryImage &&  $secondaryImage->medium ? 
                 asset('storage/' . $secondaryImage->medium)
                : null,
            'short_desc' => $short_description,
            'price' => $variants->count() > 0 ? $currencyService->convertWithSymbol($variants->first()->price, null, $request) : null,
            'colors' => $colorValues,
            'colors_without_qty' => $colorsWithoutStock,
        ];
    }
}
