<?php

namespace App\Notifications;

use App\Jobs\MixpanelJob;
use App\Models\ProjectMemberManagement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class InviteMemberNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $emailData;

    /**
     * Create a new notification instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail', FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $fetchDetail = ProjectMemberManagement::where('email', $this->emailData['invitee_email'])->update(['email_status' => '1']);

        if ($this->emailData['component'] == 'organization') {
            return (new MailMessage())
            ->subject($this->emailData['subject'])
            ->view('email.member_manager_invite_in_organisation', ['emailData' => $this->emailData]);
        } elseif ($this->emailData['component'] == 'project') {
            return (new MailMessage())
            ->subject($this->emailData['subject'])
            ->view('email.member_manager_invite_project_users', ['emailData' => $this->emailData]);
        } else {
            return (new MailMessage())
            ->subject($this->emailData['subject'])
            ->view('email.member_manager_invite_users', ['emailData' => $this->emailData]);
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function toFcm($notifiable)
    {
        $notification_data = [
            'title' => $this->emailData['subject'],
            'body'  => $this->emailData['body'],
            'url'   => '',
        ];
        MixpanelJob::dispatch(config('mixpanel.push_notification'), $notification_data, auth()->user());

        return FcmMessage::create()
            ->setData([
                'title' => $this->emailData['subject'],
                'body'  => $this->emailData['body'],
            ])
            ->setNotification([
                'title' => $this->emailData['subject'],
                'body'  => $this->emailData['body'],
                'sound' => true,
            ]);
    }
}
