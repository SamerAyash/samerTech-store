<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\ApplyDiscountRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Requests\RemoveCartItemRequest;
use App\Services\CartService;
use App\Services\CurrencyService;
use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Cart Controller
 *
 * Handles all cart-related API endpoints.
 * All endpoints require authentication via Sanctum.
 */
class CartController extends Controller
{
    protected CartService $cartService;
    protected CurrencyService $currencyService;

    /**
     * Discount service instance.
     *
     * @var DiscountService
     */
    protected DiscountService $discountService;

    /**
     * Create a new controller instance.
     *
     * @param CartService $cartService
     * @param CurrencyService $currencyService
     * @param DiscountService $discountService
     */
    public function __construct(CartService $cartService, CurrencyService $currencyService, DiscountService $discountService)
    {
        $this->cartService = $cartService;
        $this->currencyService = $currencyService;
        $this->discountService = $discountService;
    }

    /**
     * Get user's or guest's cart.
     *
     * GET /api/cart
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $guestId = $request->header('X-Guest-Id');
            
            if (!$user && !$guestId) {
                return response()->json([
                    'message' => 'Either authentication or guest ID is required',
                    'errors' => [],
                ], 401);
            }
            
            $cart = $this->cartService->getCart($user, $request, $guestId);

            return response()->json($cart);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve cart',
                'errors' => [],
            ], 500);
        }
    }

    /**
     * Add item to cart.
     *
     * POST /api/cart/add
     *
     * @param AddToCartRequest $request
     * @return JsonResponse
     */
    public function add(AddToCartRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $guestId = $request->header('X-Guest-Id');
            
            if (!$user && !$guestId) {
                return response()->json([
                    'message' => 'Either authentication or guest ID is required',
                    'errors' => [],
                ], 401);
            }
            
            $this->cartService->addItem($user, $request->validated(), $guestId);
            $result = $this->cartService->getCart($user, $request, $guestId);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => [],
            ], $statusCode);
        }
    }

    /**
     * Update cart item quantity.
     *
     * PUT /api/cart/update
     *
     * @param UpdateCartItemRequest $request
     * @return JsonResponse
     */
    public function update(UpdateCartItemRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $guestId = $request->header('X-Guest-Id');
            
            if (!$user && !$guestId) {
                return response()->json([
                    'message' => 'Either authentication or guest ID is required',
                    'errors' => [],
                ], 401);
            }
            
            $data = $request->validated();

            $result = $this->cartService->updateItem(
                $user,
                $data['item_id'],
                $data['quantity'],
                $guestId
            );

            return response()->json($result);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;

            return response()->json([
                'message' => "Failed to update cart item",
                'errors' => [],
            ], $statusCode);
        }
    }

    /**
     * Remove item from cart.
     *
     * DELETE /api/cart/remove
     *
     * @param RemoveCartItemRequest $request
     * @return JsonResponse
     */
    public function remove(RemoveCartItemRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $guestId = $request->header('X-Guest-Id');
            
            if (!$user && !$guestId) {
                return response()->json([
                    'message' => 'Either authentication or guest ID is required',
                    'errors' => [],
                ], 401);
            }
            
            $data = $request->validated();

            $result = $this->cartService->removeItem($user, $data['item_id'], $guestId);

            return response()->json($result);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => [],
            ], $statusCode);
        }
    }

    /**
     * Clear entire cart.
     *
     * DELETE /api/cart/clear
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $guestId = $request->header('X-Guest-Id');
            
            if (!$user && !$guestId) {
                return response()->json([
                    'message' => 'Either authentication or guest ID is required',
                    'errors' => [],
                ], 401);
            }

            $result = $this->cartService->clearCart($user, $guestId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to clear cart',
                'errors' => [],
            ], 500);
        }
    }

    /**
     * Apply discount code to cart.
     *
     * POST /api/cart/apply-discount
     *
     * @param ApplyDiscountRequest $request
     * @return JsonResponse
     */
    public function applyDiscount(ApplyDiscountRequest $request)
    {
        try {
            $user = $request->user();
            $guestId = $request->header('X-Guest-Id');
            
            if (!$user && !$guestId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Either authentication or guest ID is required',
                    'errors' => [],
                ], 401);
            }
            
            $locale = get_api_locale($request);

            $currency = $this->currencyService->getCurrency($request);
            $cart = $this->cartService->getCart($user, $request, $guestId);
            $cartTotal = $cart['totals']['total'] ?? 0;
            if ($cartTotal <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => $locale === 'ar' ? 'سلة التسوق فارغة' : 'Your cart is empty',
                    'errors' => [
                        'cart' => [$locale === 'ar' ? 'سلة التسوق فارغة' : 'Cart is empty']
                    ],
                ], 400);
            }
            // Apply discount (pass currency to convert discount from QAR to selected currency)
            $result = $this->discountService->applyDiscount(
                $user,
                $request->validated()['code'],
                $cartTotal,
                $locale,
                $currency,
                $request
            );

            return response()->json($result);
        }
        catch (\Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
            $locale = get_api_locale($request);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: ($locale === 'ar' ? 'رمز الخصم غير صحيح أو منتهي الصلاحية' : 'Invalid discount code'),
                'errors' => [
                    'code' => [$e->getMessage() ?: ($locale === 'ar' ? 'رمز الخصم غير صحيح أو منتهي الصلاحية' : 'The discount code is invalid or expired')]
                ],
            ], $statusCode);
        }
    }
}

