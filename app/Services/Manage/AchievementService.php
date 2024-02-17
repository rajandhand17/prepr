<?php

namespace App\Services\Manage;

use App\Models\UserAchievement;
use Carbon\Carbon;
use Exception;

class AchievementService
{
    public function addAchievement($projectMembers, $projectAchievement, $fetchChallenge, $projectData)
    {
        try {
            if ($projectAchievement) {
                $key = 1;
                $achievement_type = config('constants.user_achievement_type.participation_award');
                $certificate_date = (int) date('ymd');
                $olddata = $key - 1;
                $certificate_id = $olddata.'00'.$key;
                $certificate_number = $certificate_date.$certificate_id;
                foreach ($projectMembers as $projectMember) {
                    $userAchievement = new UserAchievement();
                    $userAchievement->user_id = $projectMember;
                    $userAchievement->certificate_number = $certificate_number;
                    $userAchievement->title = $projectAchievement->achievement_name;
                    $userAchievement->description = $projectAchievement->achievement_name;
                    $userAchievement->achievement_type = $achievement_type;
                    $userAchievement->module_id = $projectData->id;
                    $userAchievement->module_title = $projectData->title;
                    $userAchievement->module_parent_id = $fetchChallenge->id;
                    $userAchievement->module_parent_title = $fetchChallenge->title;
                    $userAchievement->achievement_prize = $projectAchievement->achievement_prize;
                    $userAchievement->achievement_points = $projectAchievement->achievement_points;
                    $userAchievement->achievement_image = $projectAchievement->achievement_image;
                    $userAchievement->issue_date = Carbon::now()->toDateTimeString();
                    $userAchievement->valid_date = null;
                    $userAchievement->user_notified = '0';
                    $userAchievement->promo_code = null;
                    $userAchievement->save();
                    $certificate_number++;
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
