<?php

namespace App\Services;

use App\Models\ProjectHistory;
use Exception;

class ProjectHistoryService
{
    public static function storeHistory($projectId, $userId, $activity)
    {
        try {
            $storeProjectHistory = ProjectHistory::create(['project_id' => $projectId, 'user_id' => $userId, 'activity' => $activity]);
            if ($storeProjectHistory) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function fetchProjectHistory($projectId)
    {
        try {
            $fetchProjectHistory = ProjectHistory::where('project_id', $projectId)->get();
            if (!empty($fetchProjectHistory)) {
                return $fetchProjectHistory;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
