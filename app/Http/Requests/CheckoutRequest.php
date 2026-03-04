<?php

namespace App\Http\Requests;

use App\Constants\ShippingMethods;
use App\Services\CurrencyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request for checkout/order creation.
 */
class CheckoutRequest extends FormRequest
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
        $useSameBilling = $this->input('use_same_billing_address', true);
        $currency = app(CurrencyService::class)->getCurrency($this);
        $locale = $this->header('X-Locale');
        $shippingMethods= ShippingMethods::getShippingMethods($currency, $locale, $this);
        $shippingMethodsIds = array_column($shippingMethods, 'id');
        $isGuest = !$this->user();
        
        $rules = [
            // Shipping address (always required)
            'shipping_country' => ['required', 'string', 'max:100'],
            'shipping_first_name' => ['required', 'string', 'max:50', 'min:1'],
            'shipping_last_name' => ['required', 'string', 'max:50', 'min:1'],
            'shipping_company' => ['nullable', 'string', 'max:100'],
            'shipping_address' => ['required', 'string', 'max:255', 'min:5'],
            'shipping_apartment' => ['nullable', 'string', 'max:100'],
            'shipping_city' => ['required', 'string', 'max:50', 'min:2'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9\s\-]+$/'],
            'shipping_phone' => ['required', 'string', 'max:20', 'min:7', 'regex:/^[\d\s\-\+\(\)]+$/'],

            // Payment and shipping
            'payment_method' => ['required', 'string', Rule::in(['myfatoorah'])],
            'shipping_method' => ['required', 'string', Rule::in($shippingMethodsIds)],
            'discount_code' => ['nullable', 'string', 'max:50'],
            'save_address' => ['nullable', 'boolean'],
            'use_same_billing_address' => ['nullable', 'boolean'],
        ];

        // Guest checkout requires email
        if ($isGuest) {
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        }

        // Billing address (required only if use_same_billing_address is false)
        if (!$useSameBilling) {
            $rules['billing_country'] = ['required', 'string', 'max:100'];
            $rules['billing_first_name'] = ['required', 'string', 'max:50', 'min:1'];
            $rules['billing_last_name'] = ['required', 'string', 'max:50', 'min:1'];
            $rules['billing_company'] = ['nullable', 'string', 'max:100'];
            $rules['billing_address'] = ['required', 'string', 'max:255', 'min:5'];
            $rules['billing_apartment'] = ['nullable', 'string', 'max:100'];
            $rules['billing_city'] = ['required', 'string', 'max:50', 'min:2'];
            $rules['billing_postal_code'] = ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9\s\-]+$/'];
            $rules['billing_phone'] = ['required', 'string', 'max:20', 'min:7', 'regex:/^[\d\s\-\+\(\)]+$/'];
        }

        return $rules;
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
                // Shipping address
                'shipping_country.required' => 'The shipping country field is required.',
                'shipping_first_name.required' => 'The shipping first name field is required.',
                'shipping_last_name.required' => 'The shipping last name field is required.',
                'shipping_address.required' => 'The shipping address field is required.',
                'shipping_city.required' => 'The shipping city field is required.',
                'shipping_phone.required' => 'The shipping phone field is required.',
                'shipping_phone.min' => 'The shipping phone must be at least 7 characters.',
                'shipping_phone.regex' => 'The shipping phone format is invalid.',

                // Billing address
                'billing_country.required' => 'The billing country field is required.',
                'billing_first_name.required' => 'The billing first name field is required.',
                'billing_last_name.required' => 'The billing last name field is required.',
                'billing_address.required' => 'The billing address field is required.',
                'billing_city.required' => 'The billing city field is required.',
                'billing_phone.required' => 'The billing phone field is required.',
                'billing_phone.min' => 'The billing phone must be at least 7 characters.',
                'billing_phone.regex' => 'The billing phone format is invalid.',

                // Payment and shipping
                'payment_method.required' => 'Please select a payment method.',
                'payment_method.in' => 'The selected payment method is invalid.',
                'shipping_method.required' => 'Please select a shipping method.',
                'shipping_method.in' => 'The selected shipping method is invalid.',
                'guest_email.required' => 'Email is required for guest checkout.',
                'guest_email.email' => 'Please provide a valid email address.',
            ],
            'ar' => [
                // Shipping address
                'shipping_country.required' => 'دولة الشحن مطلوبة.',
                'shipping_first_name.required' => 'الاسم الأول للشحن مطلوب.',
                'shipping_last_name.required' => 'اسم العائلة للشحن مطلوب.',
                'shipping_address.required' => 'عنوان الشحن مطلوب.',
                'shipping_city.required' => 'مدينة الشحن مطلوبة.',
                'shipping_phone.required' => 'هاتف الشحن مطلوب.',
                'shipping_phone.min' => 'يجب أن يكون هاتف الشحن 7 أحرف على الأقل.',
                'shipping_phone.regex' => 'تنسيق هاتف الشحن غير صحيح.',

                // Billing address
                'billing_country.required' => 'دولة الفوترة مطلوبة.',
                'billing_first_name.required' => 'الاسم الأول للفوترة مطلوب.',
                'billing_last_name.required' => 'اسم العائلة للفوترة مطلوب.',
                'billing_address.required' => 'عنوان الفوترة مطلوب.',
                'billing_city.required' => 'مدينة الفوترة مطلوبة.',
                'billing_phone.required' => 'هاتف الفوترة مطلوب.',
                'billing_phone.min' => 'يجب أن يكون هاتف الفوترة 7 أحرف على الأقل.',
                'billing_phone.regex' => 'تنسيق هاتف الفوترة غير صحيح.',

                // Payment and shipping
                'payment_method.required' => 'يرجى اختيار طريقة الدفع.',
                'payment_method.in' => 'طريقة الدفع المحددة غير صحيحة.',
                'shipping_method.required' => 'يرجى اختيار طريقة الشحن.',
                'shipping_method.in' => 'طريقة الشحن المحددة غير صحيحة.',
                'guest_email.required' => 'البريد الإلكتروني مطلوب للدفع كضيف.',
                'guest_email.email' => 'يرجى تقديم عنوان بريد إلكتروني صحيح.',
            ],
        ];

        return $messages[$locale] ?? $messages['en'];
    }
}
