<?php

namespace App\Http\Controllers\Api;

use App\Constants\ShippingMethods;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Checkout Controller
 *
 * Handles all checkout-related API endpoints.
 * All endpoints require authentication via Sanctum.
 */
class CheckoutController extends Controller
{

    public function checkout_information(Request $request, CurrencyService $currencyService)
    {
        $user = $request->user();
        $default_address = $user ? $user->default_address() : null;
        $currency = $currencyService->getCurrency($request);
        $locale = $request->header('X-Locale');
        $shippingMethods= ShippingMethods::getShippingMethods($currency, $locale, $request);
        return response()->json([
            'success' => true,
            'default_address' => $default_address,
            'shipping_methods' => $shippingMethods,
        ],200);
    }
    /**
     * Create order from cart.
     *
     * POST /api/checkout
     *
     * @param CheckoutRequest $request
     * @return JsonResponse
     */
    public function createOrder(CheckoutRequest $request,CheckoutService $checkoutService): JsonResponse
    {
        try {
            $user = $request->user();
            $guestId = $request->header('X-Guest-Id');
            $locale = get_api_locale($request);

            // Validate that either user is authenticated or guest_id is provided
            if (!$user && !$guestId) {
                return response()->json([
                    'success' => false,
                    'message' => $locale === 'ar' ? 'يجب تسجيل الدخول أو توفير معرف الضيف' : 'Either authentication or guest ID is required',
                    'errors' => ['guest_id' => [$locale === 'ar' ? 'معرف الضيف مطلوب' : 'Guest ID is required']],
                ], 401);
            }

            $order = $checkoutService->createOrder(
                $request->validated(),
                $user,
                $request,
                $guestId
            );

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'message' => $this->getMessage('order_created_successfully', $locale),
                'payment_url' => $order->payment_url,
            ], 201);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 422;
            $locale = get_api_locale($request);

            // Check if it's a validation error with errors array
            $errors = [];
            if (method_exists($e, 'errors')) {
                $errors = $e->errors();
            } elseif (str_contains($e->getMessage(), 'validation')) {
                $errors = ['general' => [$e->getMessage()]];
            }

            return response()->json([
                'success' => false,
                'message' => $this->getMessage('order_creation_failed', $locale),
                'errors' => $errors,
            ], $statusCode);
        }
    }

    /**
     * Get localized message.
     *
     * @param string $key
     * @param string $locale
     * @return string
     */
    protected function getMessage(string $key, string $locale): string
    {
        $messages = [
            'en' => [
                'order_created_successfully' => 'Order created successfully. Redirecting to payment...',
                'order_creation_failed' => 'Failed to create order. Please try again.',
            ],
            'ar' => [
                'order_created_successfully' => 'تم إنشاء الطلب بنجاح. جاري التوجيه إلى الدفع...',
                'order_creation_failed' => 'فشل إنشاء الطلب. يرجى المحاولة مرة أخرى.',
            ],
        ];

        return $messages[$locale][$key] ?? $messages['en'][$key] ?? $key;
    }
}

