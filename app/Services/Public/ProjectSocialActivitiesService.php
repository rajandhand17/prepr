<?php

namespace App\Services\Public;

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
            return false;
        }
    }
}
