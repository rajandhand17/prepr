<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Hash;

class UserSettingService
{
    public static function updatePrivacy($request)
    {
        try {
            $user = auth()->user();
            $profileVisibilityMap = [
                'signed-in' => '2',
                'private'   => '1',
                'public'   => '0',
            ];
            $profilePrivacy = $profileVisibilityMap[$request->profile_visibility];
            UserSetting::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'profile_privacy'           => $profilePrivacy,
                    'project_privacy'           => ($request->project_visibility == 'public') ? '0' : '1',
                    'friend_request_privacy'    => ($request->friend_request == 'friend') ? '0' : '1',
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
            $user= auth()->user();
            $option = [
                "unsubscribed" => "0",
                "monthly" => "1",
                "weekly" => "2"
            ];
            $labSummary = $option[$request->lab_summary];
            $challengeSummary = $option[$request->challenge_summary];
            $challengeRecommendation = $option[$request->challenge_recommendation];
            UserSetting::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'email_subscription_notification' => ($request->communication == 'subscribed') ? '0' : '1',
                    'email_subscription_network_summary' => ($request->network_summary == 'subscribed') ? '0' : '1',
                    'email_subscription_challenge_summary' => $challengeSummary,
                    'email_subscription_lab_summary' => $labSummary,
                    'display_challenge_minionboarding' => $challengeRecommendation,
                ]
            );
            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getDetails(){
        try {
            $user =auth()->user();
            return $user;
        }catch (\Exception $e){
            return false;
        }
    }
}
