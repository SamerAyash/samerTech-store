<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
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
            'order_number'=> $this->order_number,
            'status'=> $this->status,
            'payment_status'=> $this->payment_status,
            'total'=> $this->total,
            'subtotal'=> $this->subtotal,
            'discount_amount'=> $this->discount_amount,
            'currency'=> $this->currency,
            'items'=> $this->orderItems->map(function($item){
                return $this->ItemMap($item);
            }),
            'shipping_address'=> $this->AddressMap($this->shippingAddress),
            'billing_address'=> $this->AddressMap($this->billingAddress),
            'payment_method'=> $this->payment_method,
            'shipping_method'=> $this->shipping_method,
            'shipping_cost'=> $this->shipping_cost,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
    private function AddressMap($address){
        return [
            'country'=> optional($address)->country,
            'first_name'=> optional($address)->first_name,
            'last_name'=> optional($address)->last_name,
            'company'=> optional($address)->company,
            'address'=> optional($address)->address,
            'apartment'=> optional($address)->apartment,
            'city'=> optional($address)->city,
            'postal_code'=> optional($address)->postal_code,
            'phone'=> optional($address)->phone,
        ];
    }
    private function ItemMap($item)
    {
        return [
            'id'=> $item->id,
            'product_sku'=> $item->product_sku,
            'variant_id'=> $item->product_variant_id,
            'name'=> $item->product_name,
            'price'=> $item->price,
            'quantity'=> $item->quantity,
            'image'=> $item->image,
            'color'=> $item->color,
            'size'=> $item->size,
            'attributes'=> $item->attributes,
            'currency'=> $item->currency,
            'subtotal'=> $item->subtotal
        ];
    }
}
