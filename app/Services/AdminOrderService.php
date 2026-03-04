<?php

namespace App\Services;

use App\Models\Address;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Constants\ShippingMethods;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminOrderService
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {
    }

    public function createGuestOrder(array $data): Order
    {
        DB::beginTransaction();

        try {
            $currency = $data['currency'];
            $useSameBilling = $data['use_same_billing_address'] ?? true;

            $shippingAddress = $this->createAddress($data, 'shipping', null);
            $billingAddress = $useSameBilling
                ? $shippingAddress
                : $this->createAddress($data, 'billing', null);

            $orderNumber = $this->generateOrderNumber();

            $items = $data['items'];
            $subtotal = 0;
            foreach ($items as $item) {
                $itemSubtotal = (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0);
                $subtotal += $itemSubtotal;
            }

            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            $shippingCost = (float) ($data['shipping_cost'] ?? 0);
            $total = $subtotal + $shippingCost - $discountAmount;

            $discountCodeId = null;
            if (!empty($data['discount_code'])) {
                $discountCode = DiscountCode::valid()
                    ->where('code', strtoupper($data['discount_code']))
                    ->first();
                if ($discountCode) {
                    $discountCodeId = $discountCode->id;
                    if ($discountAmount <= 0) {
                        $discountAmount = $this->resolveDiscountAmount($discountCode, $subtotal);
                    }
                    $total = $subtotal + $shippingCost - $discountAmount;
                }
            }

            $currencyRate = $this->currencyService->getRateForCurrencyOrFetch($currency);

            $orderData = [
                'order_number' => $orderNumber,
                'user_id' => null,
                'guest_id' => null,
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'],
                'guest_phone' => $data['guest_phone'],
                'status' => $data['status'] ?? 'pending',
                'payment_method' => $data['payment_method'] ?? 'manual',
                'payment_status' => $data['payment_status'] ?? 'pending',
                'shipping_method' => $data['shipping_method'],
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'currency' => $currency,
                'currency_rate' => $currencyRate,
                'discount_code_id' => $discountCodeId,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
                'notes' => $data['notes'] ?? null,
            ];

            $order = Order::create($orderData);

            foreach ($items as $item) {
                $quantity = (int) ($item['quantity'] ?? 1);
                $price = (float) ($item['price'] ?? 0);
                $itemSubtotal = $price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_sku' => $item['product_sku'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'color' => $item['color'] ?? null,
                    'size' => $item['size'] ?? null,
                    'attributes' => $item['attributes'] ?? null,
                    'image' => $item['image'] ?? null,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            if ($discountCodeId) {
                DiscountCode::find($discountCodeId)?->incrementUsage();
            }

            DB::commit();

            return $order->fresh(['shippingAddress', 'billingAddress', 'orderItems']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'general' => [$e->getMessage()],
            ]);
        }
    }

    /**
     * Create address for guest order (no user_id).
     */
    protected function createAddress(array $data, string $type, ?int $userId): Address
    {
        $prefix = $type === 'billing' ? 'billing_' : 'shipping_';

        return Address::create([
            'user_id' => $userId,
            'type' => $type,
            'country' => $data[$prefix . 'country'] ?? $data['shipping_country'],
            'first_name' => $data[$prefix . 'first_name'] ?? $data['shipping_first_name'],
            'last_name' => $data[$prefix . 'last_name'] ?? $data['shipping_last_name'],
            'company' => $data[$prefix . 'company'] ?? null,
            'address' => $data[$prefix . 'address'] ?? $data['shipping_address'],
            'apartment' => $data[$prefix . 'apartment'] ?? null,
            'city' => $data[$prefix . 'city'] ?? $data['shipping_city'],
            'postal_code' => $data[$prefix . 'postal_code'] ?? null,
            'phone' => $data[$prefix . 'phone'] ?? $data['shipping_phone'],
            'is_default' => false,
        ]);
    }

    protected function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = Order::where('order_number', 'like', "ORD-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -6);
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return "ORD-{$year}-{$newNumber}";
    }

    protected function resolveDiscountAmount(DiscountCode $code, float $subtotal): float
    {
        if ($code->discount_type === 'percentage') {
            $amount = $subtotal * ((float) $code->discount_value / 100);
            $max = $code->max_discount ? (float) $code->max_discount : $amount;
            return round(min($amount, $max), 2);
        }
        return round((float) $code->discount_value, 2);
    }

    /**
     * Return allowed currencies for admin order form.
     */
    public static function allowedCurrencies(): array
    {
        return ['SAR', 'AED', 'KWD', 'QAR', 'OMR', 'BHD', 'GBP', 'USD', 'EUR'];
    }

    /**
     * Get shipping methods with costs in given currency.
     */
    public function getShippingMethodsForCurrency(string $currency): array
    {
        return ShippingMethods::getShippingMethods($currency, 'en', null);
    }
}
