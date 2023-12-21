<?php

namespace App\Services\Manage;

use App\Models\ChallengeRequirement;
use App\Models\TemplateChallengeRequirement;
use Exception;

class ChallengeTemplateRequirementService
{
    public function createChallengeTemplateRequirement($challenge_id, $templateChallengeId)
    {
        try {
            $challengeRequirement = ChallengeRequirement::where('challenge_id', $challenge_id)->get();
            if ($challengeRequirement) {
                foreach ($challengeRequirement as $challenge) {
                    $templateChallengeRequirement = new TemplateChallengeRequirement();
                    $templateChallengeRequirement->template_challenge_id = $templateChallengeId;
                    $templateChallengeRequirement->min_rank = $challenge->min_rank;
                    $templateChallengeRequirement->project_submission_requirement_ids=json_encode($challenge->project_submission_requirement_ids);
                    $templateChallengeRequirement->max_project_submission = $challenge->max_project_submission;
                    $templateChallengeRequirement->max_project_associate = $challenge->max_project_associate;
                    $templateChallengeRequirement->min_experience = $challenge->min_experience;
                    $templateChallengeRequirement->min_imported_badges = $challenge->min_imported_badges;
                    $templateChallengeRequirement->min_achievement_counts = $challenge->min_achievement_counts;
                    $templateChallengeRequirement->allow_submit_project = $challenge->allow_submit_project;
                    $templateChallengeRequirement->requirement_program = $challenge->requirement_program;
                    $templateChallengeRequirement->complete_education_program = $challenge->complete_education_program;
                    $templateChallengeRequirement->complete_experience = $challenge->complete_experience;
                    $templateChallengeRequirement->additional_requirements = $challenge->additional_requirements;
                    $templateChallengeRequirement->save();
                }
            }
            return true;
        }catch (Exception $e) {
            dd($e);
            return false;
        }
    }
}
