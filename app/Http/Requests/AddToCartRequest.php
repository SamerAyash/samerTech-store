<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for adding items to cart.
 */
class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_sku' => [
                'required',
                'string',
                'exists:products,ref_code',
            ],
            'variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $locale = get_api_locale($this);
        
        $messages = [
            'en' => [
                'product_sku.required' => 'Product SKU is required.',
                'product_sku.exists' => 'The selected product does not exist.',
                'variant_id.required' => 'Product variant is required.',
                'variant_id.exists' => 'The selected variant does not exist.',
                'quantity.required' => 'Quantity is required.',
                'quantity.integer' => 'Quantity must be an integer.',
                'quantity.min' => 'Quantity must be at least 1.',
            ],
            'ar' => [
                'product_sku.required' => 'رمز المنتج مطلوب.',
                'product_sku.exists' => 'المنتج المحدد غير موجود.',
                'variant_id.required' => 'معرف النسخة مطلوب.',
                'variant_id.exists' => 'النسخة المحددة غير موجودة.',
                'quantity.required' => 'الكمية مطلوبة.',
                'quantity.integer' => 'يجب أن تكون الكمية رقماً صحيحاً.',
                'quantity.min' => 'يجب أن تكون الكمية على الأقل 1.',
            ],
        ];
        
        return $messages[$locale] ?? $messages['en'];
    }
}

