<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDetailsResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Show order details.
     *
     * - Authenticated users: matched by user_id
     * - Guests with X-Guest-Id header: matched by guest_id
     * - Guests with token query param: matched by access_token (secure email link)
     */
    public function show($order_number, $token): JsonResponse
    {
        $order = Order::where('order_number', $order_number)
            ->where('access_token', $token)            
            ->first();

        if (!$order) {
            return response()->json(['errors' => 'Order not found', 'message' => 'Order not found'], 404);
        }

        $order->load(['orderItems', 'shippingAddress', 'billingAddress']);

        return response()->json(new OrderDetailsResource($order), 200);
    }
}
