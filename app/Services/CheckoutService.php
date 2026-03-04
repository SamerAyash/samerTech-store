<?php

namespace App\Services;

use App\Constants\ShippingMethods;
use App\Events\OrderPlaced;
use App\Models\Address;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CurrencyService;
use App\Services\MyFatoorahService;
use Binafy\LaravelCart\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Checkout Service
 *
 * Handles all checkout business logic including:
 * - Order creation
 * - Address management
 * - Stock validation
 * - Price calculation
 * - Payment processing
 */
class CheckoutService
{
    protected CartService $cartService;
    protected DiscountService $discountService;
    protected MyFatoorahService $myFatoorahService;
    protected CurrencyService $currencyService;

    public function __construct(
        CartService $cartService,
        DiscountService $discountService,
        MyFatoorahService $myFatoorahService,
        CurrencyService $currencyService
    ) {
        $this->cartService = $cartService;
        $this->discountService = $discountService;
        $this->myFatoorahService = $myFatoorahService;
        $this->currencyService = $currencyService;
    }

    /**
     * Create order from cart.
     *
     * @param array $data
     * @param User|null $user
     * @param Request $request
     * @param string|null $guestId
     * @return Order
     * @throws \Exception
     */
    public function createOrder(array $data, ?User $user, Request $request, ?string $guestId = null): Order
    {
        DB::beginTransaction();

        try {
            $locale = get_api_locale($request);
            $currency = $this->currencyService->getCurrency($request);
            $currencyRate = $this->currencyService->getRateForCurrency($currency, $request);

            // Get cart
            $cart = $this->getCartForCheckout($user, $guestId);

            if (!$cart) {
                $message = $locale === 'ar' ? 'سلة التسوق فارغة' : 'Your cart is empty. Please add items to your cart before checkout.';
                throw new \Exception($message, 400);
            }

            // Get cart items with product details
            $cartData = $this->cartService->getCart($user, $request, $guestId);
            $items = $cartData['items'] ?? [];

            if (empty($items)) {
                $message = $locale === 'ar' ? 'سلة التسوق فارغة' : 'Your cart is empty. Please add items to your cart before checkout.';
                throw new \Exception($message, 400);
            }

            // Validate stock and calculate subtotal
            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                // Validate stock
                $this->validateStock($item, $locale);

                $itemSubtotal = $item['price'] * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_sku' => $item['product_sku'] ?? $item['ref_code'] ?? '',
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['name'] ?? '',
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'color' => $item['color'] ?? null,
                    'size' => $item['size'] ?? null,
                    'attributes' => $item['attributes'] ?? null,
                    'image' => $item['image'] ?? null,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Process discount code if provided
            $discountAmount = 0;
            $discountCode = null;

            if (!empty($data['discount_code'])) {
                $discountCode = DiscountCode::valid()
                ->where('code', strtoupper($data['discount_code']))
                ->first();

                if ($discountCode) {
                    $this->discountService->validateDiscountCode($discountCode, $subtotal,$locale,$currency,$request);
                    $discountAmount = $this->discountService->calculateDiscountAmount($discountCode, $subtotal);
                }
            }

            // Calculate shipping cost
            $shippingCost = $this->calculateShippingCost($data['shipping_method'] ?? 'No-Selected', $currency);

            // Calculate total
            $total = $subtotal + $shippingCost - $discountAmount;

            // Create addresses
            $shippingAddress = $this->createAddress($user, $data, 'shipping');
            $billingAddress = $data['use_same_billing_address'] ?? true
                ? $shippingAddress
                : $this->createAddress($user, $data, 'billing');

            // Save address if requested (only for authenticated users)
            if ($user && ($data['save_address'] ?? false)) {
                $this->saveUserAddress($user, $shippingAddress);
            }

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Prepare order data
            $orderData = [
                'order_number' => $orderNumber,
                'user_id' => $user?->id,
                'guest_id' => $guestId,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'shipping_method' => $data['shipping_method'],
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'currency' => $currency,
                'currency_rate' => $currencyRate,
                'discount_code_id' => $discountCode?->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
            ];

            // Add guest details if it's a guest order
            if (!$user && $guestId) {
                $orderData['guest_name'] = ($data['shipping_first_name'] ?? '') . ' ' . ($data['shipping_last_name'] ?? '');
                $orderData['guest_email'] = $data['guest_email'] ?? null;
                $orderData['guest_phone'] = $data['shipping_phone'] ?? null;
            }

            // Create order
            $order = Order::create($orderData);

            // Create order items
            foreach ($orderItems as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_sku' => $itemData['product_sku'],
                    'product_variant_id' => $itemData['product_variant_id'],
                    'product_name' => $itemData['product_name'],
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'color' => $itemData['color'],
                    'size' => $itemData['size'],
                    'attributes' => $itemData['attributes'],
                    'image' => $itemData['image'],
                    'subtotal' => $itemData['subtotal'],
                ]);
            }

            // Increment discount code usage
            if ($discountCode) {
                $discountCode->incrementUsage();
            }

            // Generate payment URL if needed
            $paymentUrl = $this->generatePaymentUrl($order, $data['payment_method']);
            if ($paymentUrl) {
                $order->update(['payment_url' => $paymentUrl]);
            }

            // Load relationships for response
            $order->load(['shippingAddress', 'billingAddress', 'orderItems', 'discountCode']);

            // Clear cart
            $cart->emptyCart();

            DB::commit();

            // Decoupled side effects: email + admin notification via event listeners.
            try {
                event(new OrderPlaced($order->id));
            } catch (\Exception $e) {
                Log::error('Failed to dispatch OrderPlaced event', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $order->fresh(['shippingAddress', 'billingAddress', 'orderItems', 'discountCode']);
        }
         catch (\Exception $e) {
            DB::rollBack();
            
            // Send notification about order creation failure
            try {
                notifyAdmins(new \App\Notifications\OrderCreationFailedNotification(
                    $e->getMessage(),
                    $user?->id ?? null,
                    ['request_data' => $data]
                ));
            } catch (\Exception $notificationError) {
                Log::error('Failed to send order creation failure notification', [
                    'error' => $notificationError->getMessage(),
                ]);
            }
            
            throw $e;
        }
    }

    /**
     * Get cart for checkout by user or guest.
     *
     * @param User|null $user
     * @param string|null $guestId
     * @return Cart|null
     */
    protected function getCartForCheckout(?User $user, ?string $guestId = null): ?Cart
    {
        if ($user) {
            return Cart::query()
                ->with(['items.itemable'])
                ->where('user_id', $user->id)
                ->first();
        } elseif ($guestId) {
            return Cart::query()
                ->with(['items.itemable'])
                ->where('guest_id', $guestId)
                ->first();
        }
        
        return null;
    }

    /**
     * Validate stock for cart item.
     *
     * @param array $item
     * @param string $locale
     * @return void
     * @throws \Exception
     */
    protected function validateStock(array $item, string $locale): void
    {
        $productSku = $item['product_sku'] ?? null;
        $variantId = $item['variant_id'] ?? null;
        $quantity = $item['quantity'] ?? 0;

        if (!$productSku) {
            $message = $locale === 'ar' ? 'المنتج غير موجود' : 'Product not found';
            throw new \Exception($message, 404);
        }
        $product = Product::active()->where('ref_code', $productSku)->first();
        if (!$product) {
            $message = $locale === 'ar' ? 'المنتج غير موجود' : 'Product not found';
            throw new \Exception($message, 404);
        }

        $variant = ProductVariant::query()
            ->where('id', $variantId)
            ->whereHas('product', fn ($q) => $q->where('ref_code', $productSku))
            ->where('is_active', true)
            ->first();

        if (!$variant) {
            $message = $locale === 'ar' ? 'متغير المنتج غير موجود' : 'Product variant not found';
            throw new \Exception($message, 404);
        }

        if ($variant->stock <= 0) {
            $message = $locale === 'ar' ? 'المنتج غير متوفر في المخزون' : 'Product is out of stock';
            throw new \Exception($message, 400);
        }

        if ($variant->stock < $quantity) {
            $message = $locale === 'ar'
                ? "المتاح: {$variant->stock}، المطلوب: {$quantity}"
                : "Only {$variant->stock} units available, but you requested {$quantity}";
            throw new \Exception($message, 400);
        }
    }

    /**
     * Calculate shipping cost.
     *
     * @param string $shippingMethod
     * @param string $country
     * @param string $currency
     * @return float
     */
    protected function calculateShippingCost(string $shippingMethod, string $currency): float
    {
        $baseShippingCost = ShippingMethods::getShippingCost($shippingMethod, $currency);
        return $baseShippingCost;
    }

    /**
     * Create address from data.
     *
     * @param User|null $user
     * @param array $data
     * @param string $type
     * @return Address
     */
    protected function createAddress(?User $user, array $data, string $type): Address
    {
        $prefix = $type === 'billing' ? 'billing_' : 'shipping_';

        return Address::create([
            'user_id' => $user?->id,
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

    /**
     * Save address to user's saved addresses.
     *
     * @param User $user
     * @param Address $address
     * @return void
     */
    protected function saveUserAddress(User $user, Address $address): void
    {
        // Check if user has any default address
        $hasDefault = Address::where('user_id', $user->id)
            ->where('type', $address->type)
            ->where('is_default', true)
            ->exists();

        // Create a new address record for saved addresses (or update existing)
        $savedAddress = Address::create([
            'user_id' => $user->id,
            'type' => $address->type,
            'country' => $address->country,
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'company' => $address->company,
            'address' => $address->address,
            'apartment' => $address->apartment,
            'city' => $address->city,
            'postal_code' => $address->postal_code,
            'phone' => $address->phone,
            'is_default' => !$hasDefault, // Set as default if it's the first address
        ]);
    }

    /**
     * Generate unique order number.
     *
     * @return string
     */
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

    /**
     * Generate payment URL if needed.
     *
     * @param Order $order
     * @param string $paymentMethod
     * @return string|null
     */
    protected function generatePaymentUrl(Order $order, string $paymentMethod): ?string
    {
        // If payment method requires MyFatoorah payment gateway
        if (in_array($paymentMethod, ['myfatoorah'])) {
            try {
                // Create MyFatoorah payment invoice
                $paymentResult = $this->myFatoorahService->createPaymentInvoice($order);

                if ($paymentResult['success'] && !empty($paymentResult['invoice_url'])) {
                    return $paymentResult['invoice_url'];
                }
            } catch (\Exception $e) {
                // Log error but don't fail order creation
                Log::error('Failed to generate MyFatoorah payment URL', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }
}

