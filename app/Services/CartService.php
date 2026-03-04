<?php

namespace App\Services;

use App\Http\Resources\CartProductResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Binafy\LaravelCart\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Cart Service
 * 
 * Handles all cart business logic including:
 * - Adding items to cart
 * - Updating item quantities
 * - Removing items
 * - Calculating totals
 * - Currency conversion
 */
class CartService
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {
    }

    /**
     * Get user's or guest's cart with formatted items and totals.
     *
     * @param User|null $user
     * @param Request $request
     * @param string|null $guestId
     * @return array
     */
    public function getCart(?User $user, Request $request, ?string $guestId = null): array
    {
        $cart = $this->getOrCreateCart($user, $guestId);
        $locale = get_api_locale($request);
        $currency = $this->currencyService->getCurrency($request);
        $productItems = $cart->items->where('itemable_type', ProductVariant::class);
        $variantIds = $productItems->pluck('itemable_id')->map(fn ($id) => (int) $id)->filter()->unique();

        $variants = ProductVariant::with([
            'product.translations' => fn ($q) => $q
                ->where('locale', $locale)
                ->select(['id', 'product_id', 'locale', 'name']),
        ])
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $items = [];

        foreach ($productItems as $cartItem) {
            $variant = $variants->get((int) $cartItem->itemable_id);
            if (!$variant || !$variant->product) {
                continue;
            }

            $product = $variant->product;
            $product->setRelation('translations', $product->translations);
            $product->selected_variant = $variant;
            $items[] = (new CartProductResource($product, $cartItem))->toArray($request);
        }

        $totals = $this->calculateTotals($items, $currency);

        return [
            'items' => $items,
            'totals' => ['total' => $totals, 'currency' => $currency], 
        ];
    }
    /**
     * Add item to cart.
     *
     * @param User|null $user
     * @param array $data
     * @param string|null $guestId
     * @return array
     * @throws \Exception
     */
    public function addItem(?User $user, array $data, ?string $guestId = null): array
    {
        DB::beginTransaction();
        
        try {
            $productSku = $data['product_sku'];
            $quantity = $data['quantity'];
            
            // Find product
            $product = Product::where('ref_code', $productSku)->active()->first();
            
            if (!$product) {
                throw new \Exception('Product not found', 404);
            }
            
            // Find selected product variant
            $variant = ProductVariant::query()
                ->where('id', $data['variant_id'])
                ->whereHas('product', fn ($q) => $q->where('ref_code', $productSku)->active())
                ->first();

            if (!$variant) {
                throw new \Exception('Product variant not found', 404);
            }
            
            // Validate stock
            if ($variant->stock <= 0) {
                throw new \Exception('Insufficient stock available', 400);
            }
            if ($variant->stock > 0 && $variant->stock < $quantity) {
                $quantity = $variant->stock;
            }
            
            // Check if item already exists in cart
            $existingItem = $this->findExistingCartItem($user, $guestId, $variant);

            if ($existingItem) {
                // Update quantity
                $newQuantity = $existingItem->quantity + $quantity;
                
                if ($newQuantity > $variant->stock) {
                    $newQuantity = $variant->stock;
                }
                DB::table('cart_items')
                    ->where('id', $existingItem->id)
                    ->update(['quantity' => $newQuantity]);
            } else {
                // Add new item
                $cart = $this->getOrCreateCart($user, $guestId);
                $cart->items()->create([
                    'itemable_type' => ProductVariant::class,
                    'itemable_id' => (string) $variant->id,
                    'quantity' => $quantity,
                    'options' => json_encode([
                        'variant_id' => $variant->id,
                        'attributes' => $variant->attributes ?? [],
                        'color' => data_get($variant->attributes ?? [], 'color'),
                        'size' => data_get($variant->attributes ?? [], 'size'),
                    ]),
                ]);
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Item added to cart successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Update cart item quantity.
     *
     * @param User|null $user
     * @param int $itemId
     * @param int $quantity
     * @param string|null $guestId
     * @return array
     * @throws \Exception
     */
    public function updateItem(?User $user, int $itemId, int $quantity, ?string $guestId = null): array
    {
        DB::beginTransaction();
        try {
            $cart = $this->getCartByUserOrGuest($user, $guestId);
            
            if (!$cart) {
                throw new \Exception('Cart not found', 404);
            }
            
            $cartItem = $cart->items()->find($itemId);
            
            if (!$cartItem) {
                throw new \Exception('Cart item not found', 404);
            }            
            // Validate quantity
            if ($quantity < 1) {
                throw new \Exception('Quantity must be at least 1', 400);
            }
            $variant = ProductVariant::find((int) $cartItem->itemable_id);
            if (!$variant || !$variant->is_active) {
                throw new \Exception('Product variant not found', 404);
            }

            if ($quantity > (int) $variant->stock) {
                throw new \Exception('Quantity exceeds available stock', 400);
            }

            DB::table('cart_items')
                ->where('id', $cartItem->id)
                ->update(['quantity' => $quantity]);

            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Cart item updated successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Remove item from cart.
     *
     * @param User|null $user
     * @param int $itemId
     * @param string|null $guestId
     * @return array
     * @throws \Exception
     */
    public function removeItem(?User $user, int $itemId, ?string $guestId = null): array
    {
        DB::beginTransaction();
        
        try {
            $cart = $this->getCartByUserOrGuest($user, $guestId);
            
            if (!$cart) {
                throw new \Exception('Cart not found', 404);
            }
            
            $cartItem = $cart->items()->find($itemId);
            
            if (!$cartItem) {
                throw new \Exception('Cart item not found', 404);
            }
            
            $cartItem->delete();
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Item removed from cart successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Clear entire cart.
     *
     * @param User|null $user
     * @param string|null $guestId
     * @return array
     */
    public function clearCart(?User $user, ?string $guestId = null): array
    {
        DB::beginTransaction();
        
        try {
            $cart = $this->getCartByUserOrGuest($user, $guestId);
            
            if (!$cart) {
                // Cart doesn't exist, consider it already cleared
                DB::commit();
                return [
                    'success' => true,
                    'message' => 'Cart cleared successfully',
                ];
            }
            
            $cart->emptyCart();
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Cart cleared successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Find existing cart item matching selected variant.
     */
    private function findExistingCartItem(?User $user, ?string $guestId, ProductVariant $variant): ?\Illuminate\Database\Eloquent\Model
    {
        $cart = $this->getCartByUserOrGuest($user, $guestId);
        
        if (!$cart) {
            return null;
        }
        
        return $cart->items()
            ->where('itemable_type', ProductVariant::class)
            ->where('itemable_id', (string) $variant->id)
            ->first();
    }

    /**
     * Get or create cart for user or guest.
     *
     * @param User|null $user
     * @param string|null $guestId
     * @return Cart
     */
    private function getOrCreateCart(?User $user, ?string $guestId = null): Cart
    {
        if ($user) {
            return Cart::query()
                ->with(['items.itemable'])
                ->firstOrCreate(['user_id' => $user->id]);
        } elseif ($guestId) {
            return Cart::query()
                ->with(['items.itemable'])
                ->firstOrCreate(['guest_id' => $guestId]);
        }
        
        throw new \Exception('Either user or guest_id must be provided', 400);
    }

    /**
     * Get cart by user or guest.
     *
     * @param User|null $user
     * @param string|null $guestId
     * @return Cart|null
     */
    private function getCartByUserOrGuest(?User $user, ?string $guestId = null): ?Cart
    {
        if ($user) {
            return Cart::query()->where('user_id', $user->id)->first();
        } elseif ($guestId) {
            return Cart::query()->where('guest_id', $guestId)->first();
        }
        
        return null;
    }
    
    /**
     * Calculate cart totals.
     *
     * @param array $items
     * @param string $currency
     * @return float
     */
    private function calculateTotals(array $items, string $currency): float
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Get tax rate from config (default 10%)
        $taxRate = 0;//config('cart.tax_rate', 0.10);
        $tax = round($subtotal * $taxRate, 2);
        
        // Discount is 0 by default (can be extended later)
        $discount = 0;
        
        $total = round($subtotal + $tax - $discount, 2);
        return $total;
        /*return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'currency' => $currency,
        ];*/
    }
}
