<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MemberInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var int
     */
    protected int $moduleId;

    /**
     * @var int
     */
    protected int $invitationFromId;

    /**
     * @var string
     */
    protected string $moduleType;

    /**
     * @var mixed
     */
    protected mixed $extra;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $moduleType, int $moduleId, int $invitationFromId, $extra = null)
    {
        $this->moduleType = $moduleType;
        $this->moduleId = $moduleId;
        $this->invitationFromId = $invitationFromId;
        $this->extra = $extra;
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
        return $this->moduleType;
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(): array
    {
        $data = [
            'module_type' => $this->moduleType,
            'module_id' => $this->moduleId,
            'type' => data_get(NotificationTypes::MEMBER_INVITATION, $this->moduleType),
            'invitation_from_id' => $this->invitationFromId
        ];

        if ($this->extra) {
            $data['additional'] = $this->extra;
        }

        return $data;
    }
}
