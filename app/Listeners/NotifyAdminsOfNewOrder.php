<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\Order;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Log;

class NotifyAdminsOfNewOrder
{
    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $order = Order::find($event->orderId);

        if (!$order) {
            return;
        }

        try {
            notifyAdmins(new NewOrderNotification($order));
        } catch (\Exception $e) {
            Log::error('Failed to send new order admin notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
