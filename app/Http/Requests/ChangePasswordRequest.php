<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            
            // Check if current password is correct
            if (!Hash::check($this->current_password, $user->password)) {
                $validator->errors()->add('current_password', 'The current password is incorrect.');
            }

            // Check if new password is different from current password
            if (Hash::check($this->new_password, $user->password)) {
                $validator->errors()->add('new_password', 'The new password must be different from the current password.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password_confirmation.required' => 'New password confirmation is required.',
            'new_password_confirmation.min' => 'New password confirmation must be at least 8 characters.',
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
