<?php

namespace App\Http\Requests\Admin;

use App\Services\AdminOrderService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreGuestOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currencies = AdminOrderService::allowedCurrencies();

        $rules = [
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'currency' => ['required', 'string', Rule::in($currencies)],
            'use_same_billing_address' => ['nullable', 'boolean'],

            'shipping_country' => ['required', 'string', 'max:100'],
            'shipping_first_name' => ['required', 'string', 'max:50'],
            'shipping_last_name' => ['required', 'string', 'max:50'],
            'shipping_company' => ['nullable', 'string', 'max:100'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_apartment' => ['nullable', 'string', 'max:100'],
            'shipping_city' => ['required', 'string', 'max:50'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_phone' => ['required', 'string', 'max:20', 'regex:/^[\d\s\-\+\(\)]+$/'],

            'shipping_method' => ['required', 'string', Rule::in(['aramex', 'dhl'])],
            'shipping_cost' => ['required', 'numeric', 'min:0'],
            'discount_code' => ['nullable', 'string', 'max:50'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['required', 'string', Rule::in(['pending', 'paid', 'failed', 'refunded'])],
            'status' => ['required', 'string', Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:1000'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_sku' => ['required', 'string', 'max:100'],
            'items.*.variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.color' => ['nullable', 'string', 'max:50'],
            'items.*.size' => ['nullable', 'string', 'max:50'],
            'items.*.attributes' => ['nullable', 'array'],
        ];

        if (!$this->boolean('use_same_billing_address')) {
            $rules['billing_country'] = ['required', 'string', 'max:100'];
            $rules['billing_first_name'] = ['required', 'string', 'max:50'];
            $rules['billing_last_name'] = ['required', 'string', 'max:50'];
            $rules['billing_company'] = ['nullable', 'string', 'max:100'];
            $rules['billing_address'] = ['required', 'string', 'max:255'];
            $rules['billing_apartment'] = ['nullable', 'string', 'max:100'];
            $rules['billing_city'] = ['required', 'string', 'max:50'];
            $rules['billing_postal_code'] = ['nullable', 'string', 'max:20'];
            $rules['billing_phone'] = ['required', 'string', 'max:20', 'regex:/^[\d\s\-\+\(\)]+$/'];
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $items = $this->input('items', []);
            foreach ($items as $i => $item) {
                if (empty($item['product_sku']) || empty($item['product_name'])) {
                    $validator->errors()->add("items.{$i}.product_sku", __('At least one item must have product SKU and name.'));
                    break;
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'guest_name' => 'Guest name',
            'guest_email' => 'Guest email',
            'guest_phone' => 'Guest phone',
            'items.*.product_sku' => 'Product SKU',
            'items.*.product_name' => 'Product name',
            'items.*.quantity' => 'Quantity',
            'items.*.price' => 'Unit price',
        ];
    }
}
