<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'access_token' => $this->access_token,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'shipping_method' => $this->shipping_method,
            'total' => $this->total,
            'currency' => $this->currency,
            'currency_rate' => $this->currency_rate,
            'total_in_base_currency' => $this->total_in_base_currency,
            'items' => $this->whenCounted('order_items'),
            'created_at' => $this->created_at?->toISOString()
        ];
    }
}
