<?php

namespace App\Http\Resources;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mainImage = $this->mainImage;
        $firstVariant = $this->firstVariant;
        
        // Get translation for the requested locale only
        $locale = get_api_locale($request);
        $translation = $this->translations->where('locale', $locale)->first();
        $name = optional($translation)->name ?? null;
        $short_description = optional($translation)->short_description ?? null;
        
        $currencyService = app(CurrencyService::class);
        
        return [
            'ref_code' => $this->ref_code,
            'name' => $name,
            'slug' => $name ? arabic_slug($name) : null,
            'main_image' => $mainImage &&  $mainImage->medium ? 
                 asset('storage/' . $mainImage->medium)
                : null,
            'short_desc' => $short_description,
            'price' => $firstVariant ? $currencyService->convertWithSymbol($firstVariant->price, null, $request) : null
        ];
    }
}
