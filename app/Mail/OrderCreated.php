<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;
    public int $tries = 3;
    public int $backoff = 60;
    public function __construct(Order $order)
    {
        $this->order = $order->load([
            'orderItems',
            'shippingAddress',
            'billingAddress',
            'discountCode',
            'user',
        ]);
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - ' . $this->order->order_number,
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
            with: [
                'order' => $this->order,
            ],
        );
    }

    public static function getRecipientEmail(Order $order): ?string
    {
        if ($order->user && $order->user->email) {
            return $order->user->email;
        }

        if ($order->guest_email) {
            return $order->guest_email;
        }

        return null;
    }
}
