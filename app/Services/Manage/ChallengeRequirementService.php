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

    public function updateChallengeRequirement($request, $challenge_id)
    {
        try {
            $challengeRequirement = ChallengeRequirement::where('challenge_id', $challenge_id)->first();
            if (!$challengeRequirement) {
                $challengeRequirement = new ChallengeRequirement();
                $challengeRequirement->challenge_id = $challenge_id;
                $challengeRequirement->min_rank = $request->min_rank;
                $challengeRequirement->min_points = $request->min_points;
                $challengeRequirement->project_submission_requirement_ids = $request->project_submission_requirement_ids;
                $challengeRequirement->max_project_submission = $request->max_project_submission;
                $challengeRequirement->min_experience = $request->min_experience;
                $challengeRequirement->min_imported_badges = $request->min_imported_badges;
                $challengeRequirement->min_achievement_counts = $request->min_achievement_counts;
                $challengeRequirement->save();

                return true;
            }

            $challengeRequirement->min_rank = $request->has('min_rank') ? $request->min_rank : $challengeRequirement->min_rank ;
            $challengeRequirement->min_points = $request->has('min_points') ? $request->min_points : $challengeRequirement->min_points ;
            $challengeRequirement->max_project_submission = $request->has('max_project_submission') ? $request->max_project_submission : $challengeRequirement->max_project_submission ;
            $challengeRequirement->min_experience = $request->has('min_experience') ? $request->min_experience : $challengeRequirement->min_experience ;
            $challengeRequirement->min_imported_badges = $request->has('min_imported_badges') ? $request->min_imported_badges : $challengeRequirement->min_imported_badges ;
            $challengeRequirement->min_achievement_counts = $request->has('min_achievement_counts') ? $request->min_achievement_counts : $challengeRequirement->min_achievement_counts ;
            $challengeRequirement->project_submission_requirement_ids = $request->has('project_submission_requirement_ids') ? $request->project_submission_requirement_ids : $challengeRequirement->project_submission_requirement_ids;
            $challengeRequirement->save();

            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}
