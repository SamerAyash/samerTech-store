<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSliderProductRequest extends FormRequest
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
        return [
            'slider_type' => ['required', 'in:fall_winter,spring_summer'],
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('slider_products', 'product_id')->where(function ($query) {
                    return $query->where('slider_type', $this->slider_type);
                })
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.unique' => 'This product is already in the selected slider.',
            'product_id.exists' => 'The selected product does not exist.',
        ];
    }
}
