<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'communication'                       => ($this->manage_alerts == 0) ? 'unsubscribed' : 'subscribed',
            'network_summary'                     => ($this->email_subscription_network_summary == 0) ? 'unsubscribed' : 'subscribed',
            'challenge_summary'=> config('constants.subscription_options.'.$this->email_subscription_challenge_summary),
            'lab_summary'      => config('constants.subscription_options.'.$this->email_subscription_lab_summary),
            'challenge_recommendation'                => config('constants.subscription_options.'.$this->challenge_recommends),
        ];
    }
}
