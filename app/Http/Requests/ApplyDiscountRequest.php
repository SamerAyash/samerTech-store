<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request for applying discount code.
 */
class ApplyDiscountRequest extends FormRequest
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
            'code' => [
                'required',
                'string',
                'max:50',
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
                'code.required' => 'The discount code field is required.',
                'code.string' => 'The discount code must be a string.',
                'code.max' => 'The discount code may not be greater than 50 characters.',
            ],
            'ar' => [
                'code.required' => 'رمز الخصم مطلوب.',
                'code.string' => 'يجب أن يكون رمز الخصم نصاً.',
                'code.max' => 'لا يمكن أن يتجاوز رمز الخصم 50 حرفاً.',
            ],
        ];
        
        return $messages[$locale] ?? $messages['en'];
    }
}
