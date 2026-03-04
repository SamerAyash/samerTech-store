<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreationFailedNotification extends Notification
{
    use Queueable;

    protected string $error;
    protected ?int $userId;
    protected array $context;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $error, ?int $userId = null, array $context = [])
    {
        $this->error = $error;
        $this->userId = $userId;
        $this->context = $context;
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
            'type' => 'order_creation_failed',
            'title' => 'Order Creation Failed',
            'message' => "Failed to create a new order: {$this->error}",
            'error' => $this->error,
            'user_id' => $this->userId,
            'context' => $this->context,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
