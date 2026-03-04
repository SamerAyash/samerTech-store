<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array for homepage.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = get_api_locale($request);
        $translation = $this->translate($locale);
        
        return [
            'id' => $this->id,
            'title' => $translation->title ?? '',
            'slug' => $translation->slug ?? '',
            'excerpt' => $translation->excerpt ?? '',
            'featured_image' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
