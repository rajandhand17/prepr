<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeRequirement;
use App\Models\ChallengeTemplateRequirement;
use Exception;

class ChallengeTemplateRequirementService
{
    public function addChallengeTemplateRequirement($challenge_id, $templateChallengeId)
    {
        try {
            $challengeRequirement = ChallengeRequirement::where('challenge_id', $challenge_id)->get();
            if ($challengeRequirement) {
                foreach ($challengeRequirement as $challenge) {
                    $ChallengeTemplateRequirement = new ChallengeTemplateRequirement();
                    $ChallengeTemplateRequirement->challenge_template_id = $templateChallengeId;
                    $ChallengeTemplateRequirement->min_rank = $challenge->min_rank;
                    $ChallengeTemplateRequirement->project_submission_requirement_ids = $challenge->project_submission_requirement_ids;
                    $ChallengeTemplateRequirement->max_project_submission = $challenge->max_project_submission;
                    $ChallengeTemplateRequirement->max_project_associate = $challenge->max_project_associate;
                    $ChallengeTemplateRequirement->min_experience = $challenge->min_experience;
                    $ChallengeTemplateRequirement->min_imported_badges = $challenge->min_imported_badges;
                    $ChallengeTemplateRequirement->min_achievement_counts = $challenge->min_achievement_counts;
                    $ChallengeTemplateRequirement->allow_submit_project = $challenge->allow_submit_project;
                    $ChallengeTemplateRequirement->requirement_program = $challenge->requirement_program;
                    $ChallengeTemplateRequirement->complete_education_program = $challenge->complete_education_program;
                    $ChallengeTemplateRequirement->complete_experience = $challenge->complete_experience;
                    $ChallengeTemplateRequirement->additional_requirements = $challenge->additional_requirements;
                    $ChallengeTemplateRequirement->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function redeemChallengeTemplateRequirement($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateRequirements = ChallengeTemplateRequirement::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateRequirements)) {
                foreach ($checkChallengeTemplateRequirements as $challengeTemplateRequirement) {
                    $newChallengeRequirements = new ChallengeRequirement();
                    $newChallengeRequirements->challenge_id = $redeemChallengeId;
                    $newChallengeRequirements->min_rank = $challengeTemplateRequirement->min_rank;
                    $newChallengeRequirements->min_points = $challengeTemplateRequirement->min_points;
                    $newChallengeRequirements->project_submission_requirement_ids = $challengeTemplateRequirement->project_submission_requirement_ids;
                    $newChallengeRequirements->max_project_submission = $challengeTemplateRequirement->max_project_submission;
                    $newChallengeRequirements->max_project_associate = $challengeTemplateRequirement->max_project_associate;
                    $newChallengeRequirements->min_experience = $challengeTemplateRequirement->min_experience;
                    $newChallengeRequirements->min_imported_badges = $challengeTemplateRequirement->min_imported_badges;
                    $newChallengeRequirements->min_achievement_counts = $challengeTemplateRequirement->min_achievement_counts;
                    $newChallengeRequirements->allow_submit_project = $challengeTemplateRequirement->allow_submit_project;
                    $newChallengeRequirements->requirement_program = $challengeTemplateRequirement->requirement_program;
                    $newChallengeRequirements->complete_education_program = $challengeTemplateRequirement->complete_education_program;
                    $newChallengeRequirements->complete_experience = $challengeTemplateRequirement->complete_experience;
                    $newChallengeRequirements->additional_requirements = $challengeTemplateRequirement->additional_requirements;
                    $newChallengeRequirements->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteChallengeTemplateRequirement($challengeTemplateId)
    {
        try {
            $challengeTemplateRequirement = ChallengeTemplateRequirement::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateRequirement->isNotEmpty()) {
                $deleteChallengeTemplateRequirement = ChallengeTemplateRequirement::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateRequirement) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
