<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MessageDeleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $message, public $conversationId)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function viaQueues(): array
    {
        return [
            'broadcast' => 'chat',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->message,
        ]);
    }

    public function broadcastType(): string
    {
        return 'message.deleted';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
        ];
    }

    public function toDatabase()
    {
        return [
            'message' => $this->message,
        ];
    }
}
