<?php

namespace App\Services;

use App\Models\ChallengeAchievement;
use App\Models\UserAchievement;
use App\Notifications\AddWinnerAchievementNotification;
use App\Services\Public\ChallengePathService;
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
                    $user = UserService::getUserById($projectMember);
                    if ($user) {
                        $user->notify(new AddWinnerAchievementNotification(__('responses.noti_congratulations'), __('responses.noti_participation_achievement')));
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addWinnerAchievement($challengeData, $request)
    {
        try {
            if (isset($request->project_id, $request->winner_achievement_id) && is_array($request->project_id) && is_array($request->winner_achievement_id) && count($request->project_id) === count($request->winner_achievement_id)) {
                $deleteExistingAchievements = UserAchievement::where(['module_parent_id' => $challengeData->id, 'achievement_type' => config('constants.user_achievement_type.winner_award')])->delete();
                foreach ($request->project_id as $key => $value) {
                    $projectId = $request['project_id'][$key];
                    $winnerAchievementId = $request['winner_achievement_id'][$key];
                    $project = ProjectService::getProjectBasedOnUuid($projectId);
                    $fetchAcceptedMemberIds = ProjectMemberManagementService::fetchAcceptedMemberIds($project->id);
                    $fetchChallengeIncentiveAchievement = ChallengeAchievement::where(['id' => $winnerAchievementId, 'achievement_type' => '1'])->first();

                    $key = 1;
                    $achievement_type = config('constants.user_achievement_type.winner_award');
                    $certificate_date = (int) date('ymd');
                    $olddata = $key - 1;
                    $certificate_id = $olddata.'00'.$key;
                    $certificate_number = $certificate_date.$certificate_id;
                    foreach ($fetchAcceptedMemberIds as $projectMember) {
                        $userAchievement = new UserAchievement();
                        $userAchievement->user_id = $projectMember;
                        $userAchievement->certificate_number = $certificate_number;
                        $userAchievement->title = $fetchChallengeIncentiveAchievement->achievement_name;
                        $userAchievement->description = $fetchChallengeIncentiveAchievement->achievement_name;
                        $userAchievement->achievement_type = $achievement_type;
                        $userAchievement->module_id = $project->id;
                        $userAchievement->module_title = $project->title;
                        $userAchievement->module_parent_id = $challengeData->id;
                        $userAchievement->module_parent_title = $challengeData->title;
                        $userAchievement->achievement_prize = $fetchChallengeIncentiveAchievement->achievement_prize;
                        $userAchievement->achievement_points = $fetchChallengeIncentiveAchievement->achievement_points;
                        $userAchievement->achievement_image = $fetchChallengeIncentiveAchievement->achievement_image;
                        $userAchievement->issue_date = Carbon::now()->toDateTimeString();
                        $userAchievement->valid_date = null;
                        $userAchievement->user_notified = '0';
                        $userAchievement->promo_code = null;
                        $userAchievement->save();
                        $certificate_number++;
                        $user = UserService::getUserById($projectMember);
                        if ($user) {
                            $user->notify(new AddWinnerAchievementNotification(__('responses.noti_congratulations'), __('responses.noti_winner_achievement')));
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function addChallengePathAchievement($challengePathId, $userId)
    {
        try {
            $fetchChallengePath = ChallengePathService::getChallengePathBasedOnId($challengePathId);
            $key = 1;
            $achievement_type = config('constants.user_achievement_type.challenge_path');
            $certificate_date = (int) date('ymd');
            $olddata = $key - 1;
            $certificate_id = $olddata.'00'.$key;
            $certificate_number = $certificate_date.$certificate_id;

            $userAchievement = new UserAchievement();
            $userAchievement->user_id = $userId;
            $userAchievement->certificate_number = $certificate_number;
            $userAchievement->title = $fetchChallengePath->achievement->achievement_name;
            $userAchievement->description = $fetchChallengePath->achievement->achievement_name;
            $userAchievement->achievement_type = $achievement_type;
            $userAchievement->module_id = $fetchChallengePath->id;
            $userAchievement->module_title = $fetchChallengePath->title;
            $userAchievement->module_parent_id = $fetchChallengePath->getOrganization->id;
            $userAchievement->module_parent_title = $fetchChallengePath->getOrganization->title;
            $userAchievement->achievement_prize = $fetchChallengePath->achievement->achievement_name;
            $userAchievement->achievement_points = $fetchChallengePath->achievement->achievement_points;
            $userAchievement->achievement_image = $fetchChallengePath->achievement->achievement_image;
            $userAchievement->issue_date = Carbon::now()->toDateTimeString();
            $userAchievement->valid_date = null;
            $userAchievement->user_notified = '0';
            $userAchievement->promo_code = null;
            $userAchievement->save();

            $user = UserService::getUserById($userId);
            if ($user) {
                $user->notify(new AddWinnerAchievementNotification(__('responses.noti_congratulations'), __('responses.noti_challenge_path_achievement'));
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
