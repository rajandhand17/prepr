<?php

namespace App\Services;

use App\Models\UserSetting;

class UserSettingService
{
    public static function updatePrivacy($request)
    {
        try {
            $user = auth()->user();
            UserSetting::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'profile_privacy'           => config('constants.profile_visibility.'.$request->profile_visibility),
                    'project_privacy'           => config('constants.profile_visibility.'.$request->project_visibility),
                    'friend_request_privacy'    => config('constants.privacy_friend_request.'.$request->friend_request),
                ]
            );

            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateNotification($request)
    {
        try {
            $user = auth()->user();
            UserSetting::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'manage_alerts'                        => config('constants.subscribe_unsubscribe.'.$request->communication),
                    'email_subscription_network_summary'   => config('constants.subscribe_unsubscribe.'.$request->network_summary),
                    'email_subscription_challenge_summary' => config('constants.notification_options.'.$request->challenge_summary),
                    'email_subscription_lab_summary'       => config('constants.notification_options.'.$request->lab_summary),
                    'challenge_recommends'                 => config('constants.notification_options.'.$request->challenge_recommendation),
                ]
            );
            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }
}
