<?php

namespace App\Http\Resources;

use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mainImage = $this->mainImage;
        $mainCategory = $this->mainCategory->first();
        $firstVariant = $this->firstVariant;
        
        $currencyService = app(CurrencyService::class);

        return [
            'id' => $this->id,
            'ref_code' => $this->ref_code,
            'name' => [
                'en' => $this->translate('en')->name ?? null,
                'ar' => $this->translate('ar')->name ?? null,
            ],
            'brand' => [
                'en' => $this->translate('en')->brand ?? null,
                'ar' => $this->translate('ar')->brand ?? null,
            ],
            'description' => [
                'en' => $this->translate('en')->description ?? null,
                'ar' => $this->translate('ar')->description ?? null,
            ],
            'main_image' => $mainImage ? [
                'large' => $mainImage->large ? asset('storage/' . $mainImage->large) : null,
                'medium' => $mainImage->medium ? asset('storage/' . $mainImage->medium) : null,
                'small' => $mainImage->small ? asset('storage/' . $mainImage->small) : null,
            ] : null,
            'price' => $firstVariant ? $currencyService->convertFromQAR($firstVariant->price, null, $request) : null,
            'category' => $mainCategory ? [
                'id' => $mainCategory->id,
                'name' => [
                    'en' => $mainCategory->translate('en')->name ?? null,
                    'ar' => $mainCategory->translate('ar')->name ?? null,
                ],
                'slug' => [
                    'en' => $mainCategory->translate('en')->slug ?? null,
                    'ar' => $mainCategory->translate('ar')->slug ?? null,
                ],
            ] : null,
        ];
    }
}
