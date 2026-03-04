<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbandonedCartNotification extends Notification
{
    use Queueable;

    protected int $count;
    protected array $details;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $count, array $details = [])
    {
        $this->count = $count;
        $this->details = $details;
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
            'type' => 'abandoned_cart',
            'title' => 'Abandoned Cart Alert',
            'message' => "There are {$this->count} abandoned shopping carts from customers",
            'count' => $this->count,
            'details' => $this->details,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
