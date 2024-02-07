<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profilePrivacy = [
            '0'=> 'public',
            '1'=> 'private',
            '2'=> 'signed-user',
        ];
        $options = [
            '0'=> 'unsubscribed',
            '1'=> 'monthly',
            '2'=> 'weekly',
        ];

        return [
            'id'                                  => $this->id,
            'first_name'                          => $this->first_name,
            'last_name'                           => $this->last_name,
            'username'                            => $this->username,
            'email'                               => $this->email,
            'phone'                               => $this->phone_number,
            'timezone'                            => $this->preferred_timezone,
            'two_factor_verification'             => ($this->two_factor_verification == 0) ? 'disabled' : 'enabled',
            'preferred_language'                  => $this->preferred_language,
            'profile_image'                       => $this->profile_image,
            'profile_visibility'                  => $profilePrivacy[$this->userSetting->profile_privacy],
            'project_visibility'                  => ($this->userSetting->project_privacy == 0) ? 'public' : 'private',
            'friend_request_privacy'              => ($this->userSetting->friend_request_privacy == 0) ? 'any-one' : 'no-one',
            'communication'                       => ($this->userSetting->manageAlerts == 0) ? 'unsubscribed' : 'subscribed',
            'network_summary'                     => ($this->userSetting->email_subscription_network_summary == 0) ? 'unsubscribed' : 'subscribed',
            'email_subscription_challenge_summary'=> $options[$this->userSetting->email_subscription_challenge_summary],
            'email_subscription_lab_summary'      => $options[$this->userSetting->email_subscription_lab_summary],
            'challenge_recommends'                => $options[$this->userSetting->challenge_recommends],

        ];
    }
}
