<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Order Service
 *
 * Handles order completion and inventory management:
 * - Complete order after successful payment
 * - Deduct product quantities from local inventory
 * - Handle order cancellation and stock restoration
 */
class OrderService
{
    /**
     * Complete order after successful payment
     *
     * @param Order $order
     * @param string|null $paymentTransactionId
     * @return Order
     * @throws Exception
     */
    public function completeOrder(Order $order, ?string $paymentTransactionId = null): Order
    {
        DB::beginTransaction();

        try {
            // Validate order can be completed
            if ($order->payment_status === 'paid') {
                Log::warning('Order already completed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
                return $order;
            }

            $this->deductProductQuantities($order);

            // Update order status
            $order->update([
                'payment_status' => 'paid',
                'status' => 'pending',
                'payment_transaction_id' => $paymentTransactionId ?? $order->payment_transaction_id,
            ]);

            DB::commit();

            return $order->fresh(['orderItems', 'shippingAddress', 'billingAddress']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete order', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Deduct product quantities from local inventory
     *
     * @param Order $order
     * @return void
     * @throws Exception
     */
    protected function deductProductQuantities(Order $order): void
    {
        foreach ($order->orderItems as $orderItem) {
            $this->deductProductQuantity($orderItem);
        }
    }

    /**
     * Deduct quantity for a single order item
     *
     * @param OrderItem $orderItem
     * @return void
     * @throws Exception
     */
    protected function deductProductQuantity(OrderItem $orderItem): void
    {
        try {
            $quantity = $orderItem->quantity;
            if (!$orderItem->product_variant_id) {
                return;
            }
            $variant = ProductVariant::find($orderItem->product_variant_id);
            if (!$variant) {
                throw new Exception("Variant not found for order item {$orderItem->id}");
            }

            // Check if sufficient stock available
            if ($variant->stock < $quantity) {
                throw new Exception(
                    "Insufficient stock for variant {$variant->id}. " .
                    "Available: {$variant->stock}, Required: {$quantity}"
                );
            }

            $variant->decrement('stock', $quantity);

        } catch (Exception $e) {
            Log::error('Failed to deduct product quantity', [
                'order_item_id' => $orderItem->id,
                'variant_id' => $orderItem->product_variant_id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Cancel order and restore product quantities
     *
     * @param Order $order
     * @param string $reason
     * @return Order
     * @throws Exception
     */
    public function cancelOrder(Order $order, string $reason = 'Payment failed'): Order
    {
        DB::beginTransaction();

        try {
            // Only restore quantities if order was paid
            if ($order->payment_status === 'paid') {
                $this->restoreProductQuantities($order);
            }

            // Update order status
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
                'notes' => ($order->notes ? $order->notes . "\n\n" : '') . "Cancelled: {$reason}",
            ]);

            DB::commit();

            return $order->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel order', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Restore product quantities to local inventory
     *
     * @param Order $order
     * @return void
     * @throws Exception
     */
    protected function restoreProductQuantities(Order $order): void
    {
        foreach ($order->orderItems as $orderItem) {
            $this->restoreProductQuantity($orderItem);
        }
    }

    /**
     * Restore quantity for a single order item
     *
     * @param OrderItem $orderItem
     * @return void
     * @throws Exception
     */
    protected function restoreProductQuantity(OrderItem $orderItem): void
    {
        try {
            $quantity = $orderItem->quantity;
            if (!$orderItem->product_variant_id) {
                return;
            }
            $variant = ProductVariant::find($orderItem->product_variant_id);
            if (!$variant) {
                Log::warning('Variant not found when restoring quantity', [
                    'variant_id' => $orderItem->product_variant_id,
                ]);
                return;
            }

            $variant->increment('stock', $quantity);

        } catch (Exception $e) {
            Log::error('Failed to restore product quantity', [
                'order_item_id' => $orderItem->id,
                'variant_id' => $orderItem->product_variant_id,
                'error' => $e->getMessage(),
            ]);

            // Don't throw exception for restore operations to avoid blocking cancellation
        }
    }
}

