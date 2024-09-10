<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\LabHistory;
use Exception;

class LabHistoryService
{
    public static function storeHistory($moduleId, $userId, $activity)
    {
        try {
            $storeLabHistory = LabHistory::create(['module_id' => $moduleId, 'user_id' => $userId, 'activity' => $activity]);
            if ($storeLabHistory) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchHistory($moduleId)
    {
        try {
            $fetchLabHistory = LabHistory::where('module_id', $moduleId)->latest()->get();
            if (!empty($fetchLabHistory)) {
                return $fetchLabHistory;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
