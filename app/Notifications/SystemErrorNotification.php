<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemErrorNotification extends Notification
{
    use Queueable;

    protected string $error;
    protected string $location;
    protected array $context;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $error, string $location = '', array $context = [])
    {
        $this->error = $error;
        $this->location = $location;
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
            'type' => 'system_error',
            'title' => 'System Error',
            'message' => "A system error occurred" . ($this->location ? " in: {$this->location}" : '') . " - {$this->error}",
            'error' => $this->error,
            'location' => $this->location,
            'context' => $this->context,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
