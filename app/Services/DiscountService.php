<?php

namespace App\Services;

use App\Models\DiscountCode;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Discount Service
 *
 * Handles all discount code business logic including:
 * - Validating discount codes
 * - Calculating discount amounts
 * - Checking usage limits
 */
class DiscountService
{
    /**
     * Currency service instance.
     *
     * @var CurrencyService
     */
    protected CurrencyService $currencyService;

    /**
     * Create a new service instance.
     *
     * @param CurrencyService $currencyService
     */
    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }
    /**
     * Apply discount code to cart.
     *
     * @param User|null $user
     * @param string $code
     * @param float $cartTotal
     * @param string $locale
     * @param string $currency
     * @param Request|null $request
     * @return array
     * @throws \Exception
     */
    public function applyDiscount(?User $user, string $code, float $cartTotal, string $locale = 'en', string $currency = 'QAR', ?Request $request = null): array
    {
        try {
            $discountCode = DiscountCode::where('code', strtoupper($code))->first();

            if (!$discountCode) {
                $message = $locale === 'ar' ? 'رمز الخصم غير صحيح' : 'Invalid discount code';
                throw new \Exception($message, 400);
            }

            // Convert cart total from selected currency to QAR for discount calculation
            // Discount values in database are stored in QAR
            $cartTotalQAR = $this->currencyService->convertToQAR($cartTotal, $currency, $request);

            // Validate discount code (using QAR values)
            $this->validateDiscountCode($discountCode, $cartTotalQAR, $locale,$currency,$request);

            // Calculate discount amount in QAR
            $discountAmountQAR = $discountCode->calculateDiscount($cartTotalQAR);

            // Convert discount amount to selected currency
            $discountAmount = $this->currencyService->convertFromQAR($discountAmountQAR, $currency, $request);

            $message = $locale === 'ar' ? 'تم تطبيق الخصم بنجاح' : 'Discount applied successfully';

            return [
                'success' => true,
                'discount' => round($discountAmount, 2),
                'message' => $message,
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate discount code.
     *
     * @param DiscountCode $discountCode
     * @param float $cartTotal
     * @param string $locale
     * @return void
     * @throws \Exception
     */
    public function validateDiscountCode(DiscountCode $discountCode, float $cartTotal, string $locale = 'en',string $currency = 'QAR',$request): void
    {
        if (!$discountCode->isValid()) {
            $message = $locale === 'ar' ? 'رمز الخصم غير صحيح أو منتهي الصلاحية' : 'The discount code is invalid or expired';
            throw new \Exception($message, 400);
        }

        // Check minimum purchase amount
        if ($discountCode->min_amount && $cartTotal < $discountCode->min_amount) {
            $discount_min_amount = $this->currencyService->convertFromQAR($discountCode->min_amount, $currency, $request);
            $message = $locale === 'ar'
                ? "الحد الأدنى للشراء هو {$discount_min_amount} {$currency}"
                : "Minimum purchase amount is {$discount_min_amount} {$currency}";
            throw new \Exception($message, 400);
        }
    }

    /**
     * Calculate discount amount for order.
     *
     * @param DiscountCode $discountCode
     * @param float $subtotal
     * @return float
     */
    public function calculateDiscountAmount(DiscountCode $discountCode, float $subtotal): float
    {
        return $discountCode->calculateDiscount($subtotal);
    }
}

