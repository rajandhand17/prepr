<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
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
                    'friend_request_privacy'    => config('constants.privacy_friend_request.'.$request->friend_request_privacy),
                ]
            );

            return $user;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
                    'manage_alerts'                        => $request->communication ? config('constants.subscribe_unsubscribe.'.$request->communication) : config('constants.subscribe_unsubscribe.unsubscribe'),
                    'email_subscription_network_summary'   => $request->network_summary ? config('constants.notification_options.'.$request->network_summary) : config('constants.subscribe_unsubscribe.unsubscribe'),
                    'email_subscription_challenge_summary' => $request->challenge_summary ? config('constants.notification_options.'.$request->challenge_summary) : config('constants.notification_options.unsubscribe'),
                    'email_subscription_lab_summary'       => $request->lab_summary ? config('constants.notification_options.'.$request->lab_summary) : config('constants.notification_options.unsubscribe'),
                    'challenge_recommends'                 => $request->challenge_recommendation ? config('constants.notification_options.'.$request->challenge_recommendation) : config('constants.notification_options.unsubscribe'),
                ]
            );

            return $user;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
