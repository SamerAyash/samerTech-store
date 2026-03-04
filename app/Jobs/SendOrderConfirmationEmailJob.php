<?php

namespace App\Jobs;

use App\Mail\OrderCreated;
use App\Models\Order;
use App\Notifications\SystemErrorNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of attempts before failing permanently.
     */
    public int $tries = 3;

    /**
     * Seconds to wait before retrying.
     */
    public int $backoff = 30;

    public function __construct(public int $orderId)
    {
    }

    public function handle(): void
    {
        $order = Order::find($this->orderId);

        if (!$order) {
            return;
        }

        $customerEmail = OrderCreated::getRecipientEmail($order);
        if (!$customerEmail) {
            return;
        }

        Mail::to($customerEmail)->send(new OrderCreated($order));
    }

    public function failed(\Throwable $e): void
    {
        $order = Order::find($this->orderId);

        Log::error('Order confirmation email job failed', [
            'order_id' => $order?->id,
            'order_number' => $order?->order_number,
            'error' => $e->getMessage(),
        ]);

        try {
            notifyAdmins(new SystemErrorNotification(
                $e->getMessage(),
                'SendOrderConfirmationEmailJob::failed',
                [
                    'order_id' => $order?->id,
                    'order_number' => $order?->order_number,
                    'customer_email' => $order ? OrderCreated::getRecipientEmail($order) : null,
                ]
            ));
        } catch (\Throwable $notifyError) {
            Log::error('Failed to notify admins about order email job failure', [
                'order_id' => $order?->id,
                'error' => $notifyError->getMessage(),
            ]);
        }
    }
}
