<?php

namespace App\Notifications;

use App\Jobs\MixpanelJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class ComponentJoinedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $title;
    private $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            'title' => $this->title,
            'body'  => $this->body,
            'url'   => '',
        ];

        if (config('app.isMixPanelEnable')) {
            MixpanelJob::dispatch(config('mixpanel.push_notification'), $notification_data, auth()->user(), request()->ip());
        }

        return FcmMessage::create()
            ->setData([
                'title' => $this->title,
                'body'  => $this->body,
            ])
            ->setNotification([
                'title' => $this->title,
                'body'  => $this->body,
                'sound' => true,
            ]);
    }
}
