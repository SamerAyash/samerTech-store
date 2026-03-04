<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification
{
    use Queueable;

    protected Order $order;
    protected string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $reason = '')
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_failed',
            'title' => 'Payment Failed',
            'message' => "Payment failed for order number: {$this->order->order_number}" . ($this->reason ? " - {$this->reason}" : ''),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'currency' => $this->order->currency,
            'reason' => $this->reason,
            'user_name' => $this->order->user->name ?? 'User',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
