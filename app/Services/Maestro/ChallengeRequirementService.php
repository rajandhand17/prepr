<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeRequirement;
use Exception;

class ChallengeRequirementService
{
    public static function challengeRequirementsSave($request, $challenge)
    {
        try {
            $challengeRequirement = new ChallengeRequirement();
            $challengeRequirement->challenge_id = $challenge->id;
            $challengeRequirement->min_rank = (int) !empty($request->min_rank) ? $request->min_rank : null;
            $challengeRequirement->min_points = (int) !empty($request->min_points) ? $request->min_points : null;
            $challengeRequirement->project_submission_requirement_ids = ['2', '3'];

            if ($challengeRequirement->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
