<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\ProjectSocialActivity;
use Exception;

class ProjectSocialActivitiesService
{
    public function getFavouriteProjectIds($userId)
    {
        try {
            $getInvitedProjectIds = ProjectSocialActivity::where(['user_id' => $userId, 'favourite' => '1'])->pluck('project_id');

            return $getInvitedProjectIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getColumnNameValue($action)
    {
        try {
            $column = null;
            $value = null;
            switch ($action) {
                case 'vote':
                    $column = 'vote';
                    $value = '1';
                    break;
                case 'remove-vote':
                    $column = 'vote';
                    $value = '0';
                    break;
                case 'share':
                    $column = 'share';
                    $value = '1';
                    break;
                case 'favourite':
                    $column = 'favourite';
                    $value = '1';
                    break;
                case 'un-favourite':
                    $column = 'favourite';
                    $value = '2';
                    break;
                case 'like':
                    $column = 'like_dislike';
                    $value = '1';
                    break;
                case 'un-like':
                    $column = 'like_dislike';
                    $value = '2';
                    break;
                default:
                    $column = null;
                    $value = null;
                    break;
            }
            if ($column != null && $value != null) {
                return ['column' => $column, 'action' => $value];
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkSocialActivity($projectId, $column, $action)
    {
        try {
            $checkActivity = ProjectSocialActivity::where(
                [
                    'project_id'      => $projectId,
                    'user_id'         => auth()->user()->id,
                    $column           => $action,
                ]
            )->first();
            if ($checkActivity != null) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function captureSocialActivity($projectId, $column, $action)
    {
        try {
            if (auth()->check()) {
                ProjectSocialActivity::updateOrInsert([
                    'user_id'       => auth()->user()->id,
                    'project_id'    => $projectId,
                ], [
                    $column => $action,
                ]);

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
