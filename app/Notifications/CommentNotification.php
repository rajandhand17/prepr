<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    protected string $module;

    /**
     * @var int
     */
    protected int $moduleId;

    /**
     * @var int
     */
    protected int $userId;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $module, int $moduleId, int $userId)
    {
        $this->module = $module;
        $this->moduleId = $moduleId;
        $this->userId = $userId;
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
        return NotificationTypes::COMMENT;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'module_type' => $this->module,
            'module_id'   => $this->moduleId,
            'user_id'     => $this->userId,
        ];
    }
}
