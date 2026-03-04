<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $user = $this->user();

        return [
            'name' => 'sometimes|string|max:50',
            'phone' => 'sometimes|string|max:20',
            'gender' => 'sometimes|in:male,female',
            'birth_date' => 'sometimes|date|before_or_equal:' . now()->subYears(18)->toDateString(),
            'country' => 'sometimes|string|max:50',
            'city' => 'sometimes|string|max:50',
            'main_address' => 'sometimes|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.max' => 'First name must not exceed 50 characters.',
            'last_name.max' => 'Last name must not exceed 50 characters.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'gender.in' => 'Gender must be either male or female.',
            'birth_date.date' => 'Birth date must be a valid date.',
            'birth_date.before_or_equal' => 'You must be at least 18 years old.',
            'country.max' => 'Country must not exceed 50 characters.',
            'city.max' => 'City must not exceed 50 characters.',
            'main_address.max' => 'Main address must not exceed 255 characters.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $locale = get_api_locale($this);
        $message = $locale === 'ar' 
            ? 'فشل التحقق من البيانات' 
            : 'Validation failed';

        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $validator->errors(),
        ], 422));
    }
}
