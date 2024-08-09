<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeAchievement;
use App\Models\UserAchievement;
use App\Notifications\AddLabAchievementNotification;
use App\Notifications\AddLabProgramAchievementNotification;
use App\Notifications\AddResourceGroupAchivementNotification;
use App\Notifications\AddWinnerAchievementNotification;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\ResourceGroupService;
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
                    $userAchievement->achievement_image = $projectAchievement->getRawOriginal('achievement_image');
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
            UtilityHelper::logError($e);

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
                        $userAchievement->achievement_image = $fetchChallengeIncentiveAchievement->getRawOriginal('achievement_image');
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
            UtilityHelper::logError($e);
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
            $userAchievement->achievement_image = $fetchChallengePath->achievement->getRawOriginal('achievement_image');
            $userAchievement->issue_date = Carbon::now()->toDateTimeString();
            $userAchievement->valid_date = null;
            $userAchievement->user_notified = '0';
            $userAchievement->promo_code = null;
            $userAchievement->save();

            $user = UserService::getUserById($userId);
            if ($user) {
                $user->notify(new AddWinnerAchievementNotification(__('responses.noti_congratulations'), __('responses.noti_challenge_path_achievement')));
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function addResourceGroupAchievement($resourceGroupId, $userId)
    {
        try {
            $fetchResourceGroup = ResourceGroupService::getResourceGroupBasedOnId($resourceGroupId);
            $key = 1;
            $achievement_type = config('constants.user_achievement_type.resource_group');
            $certificate_date = (int) date('ymd');
            $olddata = $key - 1;
            $certificate_id = $olddata.'00'.$key;
            $certificate_number = $certificate_date.$certificate_id;

            $userAchievement = new UserAchievement();
            $userAchievement->user_id = $userId;
            $userAchievement->certificate_number = $certificate_number;
            $userAchievement->title = $fetchResourceGroup->achievement->achievement_name;
            $userAchievement->description = $fetchResourceGroup->achievement->achievement_name;
            $userAchievement->achievement_type = $achievement_type;
            $userAchievement->module_id = $fetchResourceGroup->id;
            $userAchievement->module_title = $fetchResourceGroup->title;
            $userAchievement->module_parent_id = $fetchResourceGroup->getOrganization->id;
            $userAchievement->module_parent_title = $fetchResourceGroup->getOrganization->title;
            $userAchievement->achievement_prize = $fetchResourceGroup->achievement->achievement_name;
            $userAchievement->achievement_points = $fetchResourceGroup->achievement->achievement_points;
            $userAchievement->achievement_image = $fetchResourceGroup->achievement->getRawOriginal('achievement_image');
            $userAchievement->issue_date = Carbon::now()->toDateTimeString();
            $userAchievement->valid_date = null;
            $userAchievement->user_notified = '0';
            $userAchievement->promo_code = null;
            $userAchievement->save();

            $user = UserService::getUserById($userId);
            if ($user) {
                $user->notify(new AddResourceGroupAchivementNotification(__('responses.noti_congratulations'), __('responses.noti_challenge_path_achievement')));
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }


    public static function addLabAchievement($labId, $userId)
    {
        try {
            $fetchLab = LabService::getLabBasedOnId($labId);
            $key = 1;
            $achievement_type = config('constants.user_achievement_type.lab');
            $certificate_date = (int) date('ymd');
            $olddata = $key - 1;
            $certificate_id = $olddata . '00' . $key;
            $certificate_number = $certificate_date . $certificate_id;

            $userAchievement = new UserAchievement();
            $userAchievement->user_id = $userId;
            $userAchievement->certificate_number = $certificate_number;
            $userAchievement->title = $fetchLab->achievement->achievement_name;
            $userAchievement->description = $fetchLab->achievement->achievement_name;
            $userAchievement->achievement_type = $achievement_type;
            $userAchievement->module_id = $fetchLab->id;
            $userAchievement->module_title = $fetchLab->title;
            $userAchievement->module_parent_id = $fetchLab->organization->id;
            $userAchievement->module_parent_title = $fetchLab->organization->title;
            $userAchievement->achievement_prize = $fetchLab->achievement->achievement_name;
            $userAchievement->achievement_points = $fetchLab->achievement->achievement_points;
            $userAchievement->achievement_image = $fetchLab->achievement->getRawOriginal('achievement_image');
            $userAchievement->issue_date = Carbon::now()->toDateTimeString();
            $userAchievement->valid_date = null;
            $userAchievement->user_notified = '0';
            $userAchievement->promo_code = null;
            $userAchievement->save();

            $user = UserService::getUserById($userId);
            if ($user) {
                $user->notify(new AddLabAchievementNotification(__('responses.noti_congratulations'), __('responses.noti_participation_achievement')));
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function addLabProgramAchievement($labProgramId, $userId)
    {
        try {
            $fetchLabProgram = LabProgramService::getLabProgramBasedOnId($labProgramId);
            $key = 1;
            $achievement_type = config('constants.user_achievement_type.lab_program');
            $certificate_date = (int) date('ymd');
            $olddata = $key - 1;
            $certificate_id = $olddata.'00'.$key;
            $certificate_number = $certificate_date.$certificate_id;

            $userAchievement = new UserAchievement();
            $userAchievement->user_id = $userId;
            $userAchievement->certificate_number = $certificate_number;
            $userAchievement->title = $fetchLabProgram->achievement->achievement_name;
            $userAchievement->description = $fetchLabProgram->achievement->achievement_name;
            $userAchievement->achievement_type = $achievement_type;
            $userAchievement->module_id = $fetchLabProgram->id;
            $userAchievement->module_title = $fetchLabProgram->title;
            $userAchievement->module_parent_id = $fetchLabProgram->getOrganization->id;
            $userAchievement->module_parent_title = $fetchLabProgram->getOrganization->title;
            $userAchievement->achievement_prize = $fetchLabProgram->achievement->achievement_name;
            $userAchievement->achievement_points = $fetchLabProgram->achievement->achievement_points;
            $userAchievement->achievement_image = $fetchLabProgram->achievement->getRawOriginal('achievement_image');
            $userAchievement->issue_date = Carbon::now()->toDateTimeString();
            $userAchievement->valid_date = null;
            $userAchievement->user_notified = '0';
            $userAchievement->promo_code = null;
            $userAchievement->save();

            $user = UserService::getUserById($userId);
            if ($user) {
                $user->notify(new AddLabProgramAchievementNotification(__('responses.noti_congratulations'), __('responses.noti_participation_achievement')));
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
