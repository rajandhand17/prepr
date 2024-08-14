<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeRequirement;
use App\Models\ChallengeTemplateRequirement;
use Exception;

class ChallengeTemplateRequirementService
{
    public static function addChallengeTemplateRequirement($challenge_id, $templateChallengeId)
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
}
