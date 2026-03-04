<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        $locale = get_api_locale($request);
        $translation = $this->translations->where('locale', $locale)->first();
        
        return [
            'id' => optional($translation)->slug ?? null,
            'name' => optional($translation)->name ?? null
        ];
    }
}
