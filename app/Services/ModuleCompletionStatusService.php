<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\ModuleCompletionStatus;
use Exception;

class ModuleCompletionStatusService
{
    public static function checkChallengePathAchievementAssignedOrNot($challengePathId, $userId)
    {
        try {
            $checkChallengePathAchievementAssignedOrNot = ModuleCompletionStatus::where([
                'module_id'     => $challengePathId,
                'user_id'       => $userId,
                'module_type'   => '3',
            ])->exists();

            return $checkChallengePathAchievementAssignedOrNot;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function markChallengePathCompleted($challengePathId, $userId)
    {
        try {
            $markChallengePathCompleted = ModuleCompletionStatus::create([
                'module_id'     => $challengePathId,
                'user_id'       => $userId,
                'module_type'   => '3',
            ]);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
