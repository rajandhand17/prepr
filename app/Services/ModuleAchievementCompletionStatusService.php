<?php

namespace App\Services;

use App\Models\ModuleAchievementCompletionStatus;
use Exception;

class ModuleAchievementCompletionStatusService
{
    public static function checkChallengePathAchievementAssignedOrNot($challengePathId, $userId)
    {
        try {
            $checkChallengePathAchievementAssignedOrNot = ModuleAchievementCompletionStatus::where([
                'module_id'     => $challengePathId,
                'user_id'       => $userId,
                'module_type'   => '0',
            ])->exists();

            return $checkChallengePathAchievementAssignedOrNot;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function markChallengePathCompleted($challengePathId, $userId)
    {
        try {
            $markChallengePathCompleted = ModuleAchievementCompletionStatus::create([
                'module_id'     => $challengePathId,
                'user_id'       => $userId,
                'module_type'   => '0',
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
