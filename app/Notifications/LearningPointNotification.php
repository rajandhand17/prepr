<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LearningPointNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    protected string $learningPointType;

    /**
     * @var int
     */
    protected int $learningPointObtained;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $learningPointType, int $learningPointObtained)
    {
        $this->learningPointType = $learningPointType;
        $this->learningPointObtained = $learningPointObtained;
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
        return NotificationTypes::LEARNING_POINT;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'learning_points_obtained' => $this->learningPointObtained,
            'learning_point_type' => $this->learningPointType,
        ];
    }
}
