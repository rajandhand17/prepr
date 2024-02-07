<?php

namespace App\Services;

use App\Models\UserSetting;

class UserSettingService
{
    public static function updatePrivacy($request, $userDetails)
    {
        try {
            $profileVisibilityMap = [
                'signed-in' => '2',
                'private'   => '1',
                'public'    => '0',
            ];
            $friendRequestParameters = [
                'any-one' => '0',
                'no-one'  => '1',
            ];
            $profilePrivacy = $profileVisibilityMap[$request->profile_visibility];
            $projectVisibility = $profileVisibilityMap[$request->project_visibility];
            $friendRequest = $friendRequestParameters[$request->friend_request];

            UserSetting::updateOrCreate(
                ['user_id' => $userDetails->id],
                [
                    'profile_privacy'           => $profilePrivacy,
                    'project_privacy'           => $projectVisibility,
                    'friend_request_privacy'    => $friendRequest,
                ]
            );

            return $userDetails;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateNotification($request, $userDetails)
    {
        try {
            $option = [
                'unsubscribed' => '0',
                'monthly'      => '1',
                'weekly'       => '2',
            ];
            $subscriptionOrUnsubscribe = [
                'unsubscribed' => '0',
                'subscribed'   => '1',
            ];
            $labSummary = $option[$request->lab_summary];
            $challengeSummary = $option[$request->challenge_summary];
            $challengeRecommendation = $option[$request->challenge_recommendation];
            $manageAlerts = $subscriptionOrUnsubscribe[$request->communication];
            $emailSubscriptionNetworkSummary = $subscriptionOrUnsubscribe[$request->communication];
            UserSetting::updateOrCreate(
                ['user_id' => $userDetails->id],
                [
                    'manage_alerts'                        => $manageAlerts,
                    'email_subscription_network_summary'   => $emailSubscriptionNetworkSummary,
                    'email_subscription_challenge_summary' => $challengeSummary,
                    'email_subscription_lab_summary'       => $labSummary,
                    'challenge_recommends'                 => $challengeRecommendation,
                ]
            );

            return $userDetails;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getDetails()
    {
        try {
            $user = auth()->user();

            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }
}
