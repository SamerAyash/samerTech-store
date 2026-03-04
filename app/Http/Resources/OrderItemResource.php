<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_sku' => $this->product_sku,
            'variant_id' => $this->product_variant_id,
            'product_name' => $this->product_name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'color' => $this->color,
            'size' => $this->size,
            'attributes' => $this->attributes,
            'image' => $this->image,
            'subtotal' => $this->subtotal,
        ];
    }
}
