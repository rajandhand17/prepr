<?php

namespace App\Helpers;

use App\Models\User;
use App\Notifications\LearningPointNotification;

class LearningPointsHelper
{
    const LOGIN = ['type' => 'login', 'points' => 10];
    const JOIN_A_LAB = ['type' => 'join_a_lab', 'points' => 50];
    const CREATE_A_PROJECT = ['type' => 'create_a_project', 'points' => 20];
    const INVITE_MEMBER_TO_A_PROJECT = ['type' => 'invite_member_to_a_project', 'points' => 50];
    const SUBMIT_A_PROJECT = ['type' => 'submit_a_project', 'points' => 50];
    const SUCCESSFUL_PROJECT_SUBMISSION = ['type' => 'successful_project_submission', 'points' => 150];
    const LIKE_A_PROJECT = ['type' => 'like_a_project', 'points' => 20];
    const POST_A_COMMENT = ['type' => 'post_a_comment', 'points' => 20];
    const REPLY_TO_A_COMMENT = ['type' => 'reply_to_a_comment', 'points' => 20];

    const LOGIN_VIA_LINKEDIN = ['type' => 'login_via_linkedin', 'points' => 100];

    const LOGIN_VIA_MICROSOFT = ['type' => 'login_via_microsoft', 'points' => 100];

    const LOGIN_VIA_GOOGLE = ['type' => 'login_via_google', 'points' => 100];

    const LOGIN_VIA_APPLE = ['type' => 'login_via_apple', 'points' => 100];

    const LOGIN_VIA_MAGNET = ['type' => 'login_via_magnet', 'points' => 100];

    public static function sendBulkLearningPointNotification(array $users, string $type, int $point)
    {
        try {
            collect($users)->each(function (int $userId) use ($point, $type) {
                $user = User::query()->where('id', '=', $userId)->first();
                $user?->notify(new LearningPointNotification($type, $point));
            });
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
