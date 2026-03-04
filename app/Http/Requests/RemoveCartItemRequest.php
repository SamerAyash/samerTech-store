<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for removing cart item.
 */
class RemoveCartItemRequest extends FormRequest
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
            ],
            'ar' => [
                'item_id.required' => 'معرف العنصر مطلوب.',
                'item_id.exists' => 'عنصر السلة المحدد غير موجود.',
            ],
        ];
        
        return $messages[$locale] ?? $messages['en'];
    }
}

