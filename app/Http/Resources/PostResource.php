<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = get_api_locale($request);
        $translation = $this->translate($locale);
        
        $data = [
            'id' => $this->id,
            'title' => $translation->title ?? '',
            'slug' => $translation->slug ?? '',
            'content' => $translation->content ?? '',
            'excerpt' => $translation->excerpt ?? '',
            'featured_image' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'published_at' => $this->published_at?->toIso8601String()
        ];

        // Include SEO metadata
        $data['seo'] = $this->getSeoMetadata($locale);

        // Include related products if loaded
        if ($this->relationLoaded('products')) {
            $data['related_products'] = ProductCardResource::collection($this->products);
        }

        return $data;
    }
}
