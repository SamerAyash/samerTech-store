<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for updating cart item quantity.
 */
class UpdateCartItemRequest extends FormRequest
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
            'item_id' => [
                'required',
                'integer',
                'exists:cart_items,id',
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
                'item_id.required' => 'Item ID is required.',
                'item_id.exists' => 'The selected cart item does not exist.',
                'quantity.required' => 'Quantity is required.',
                'quantity.integer' => 'Quantity must be an integer.',
                'quantity.min' => 'Quantity must be at least 1.',
            ],
            'ar' => [
                'item_id.required' => 'معرف العنصر مطلوب.',
                'item_id.exists' => 'عنصر السلة المحدد غير موجود.',
                'quantity.required' => 'الكمية مطلوبة.',
                'quantity.integer' => 'يجب أن تكون الكمية رقماً صحيحاً.',
                'quantity.min' => 'يجب أن تكون الكمية على الأقل 1.',
            ],
        ];
        
        return $messages[$locale] ?? $messages['en'];
    }
}

