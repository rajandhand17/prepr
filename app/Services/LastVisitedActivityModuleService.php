<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\LastVisitedActivityModule;
use Exception;

class LastVisitedActivityModuleService
{
    public static function lastVisitedActivityModule($moduleId, $userId, $moduleType)
    {
        try {
            $lastVisitedActivityModule = LastVisitedActivityModule::create(['user_id' => $userId, 'module_id' => $moduleId, 'module_type' => $moduleType]);
            if (!$lastVisitedActivityModule) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchLastVisited($userData)
    {
        try {
            $fetchLastVisited = LastVisitedActivityModule::where('user_id', $userData->id)->latest()->first();
            if ($fetchLastVisited) {
                return $fetchLastVisited;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
