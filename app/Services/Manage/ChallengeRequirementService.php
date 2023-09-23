<?php

namespace App\Services\Manage;

use App\Models\ChallengeRequirement;
use Exception;

class ChallengeRequirementService
{
    public function createChallengeRequirement($request, $challenge)
    {
        try {
            $challengeRequirement = new ChallengeRequirement();
            $challengeRequirement->challenge_id = $challenge;
            $challengeRequirement->min_rank = $request->min_rank;
            $challengeRequirement->min_points = $request->min_points;
            $challengeRequirement->project_submission_requirement_ids = $request->project_submission_requirement_ids;
            $challengeRequirement->max_project_submission = $request->max_project_submission;
            $challengeRequirement->min_experience = $request->min_experience;
            $challengeRequirement->min_imported_badges = $request->min_imported_badges;
            $challengeRequirement->min_achievement_counts = $request->min_achievement_counts;
            $challengeRequirement->save();

            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}
