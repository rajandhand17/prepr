<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FriendRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var int
     */
    protected int $requestFromId;

    /**
     * @var int
     */
    protected int $friendRequestId;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $requestFromId, int $friendRequestId)
    {
        $this->requestFromId = $requestFromId;
        $this->friendRequestId = $friendRequestId;
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
     * @return string
     */
    public function databaseType(): string
    {
        return NotificationTypes::FRIEND_REQUEST;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(): array
    {
        return [
            'friend_request_from' => $this->requestFromId,
            'friend_request_id'   => $this->friendRequestId,
        ];
    }
}
