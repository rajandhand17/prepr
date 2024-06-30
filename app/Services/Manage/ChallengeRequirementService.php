<?php

namespace App\Services\Manage;

use App\Models\ChallengeRequirement;
use Exception;

class ChallengeRequirementService
{
    public function createChallengeRequirement($request, $challenge_id)
    {
        try {
            $allowSubmitProject = config('constants.challenge_requirement_common.no');
            switch ($request->allow_submit_project) {
                case 'yes':
                    $allowSubmitProject = config('constants.challenge_requirement_common.yes');
                    break;
                case 'no':
                    $allowSubmitProject = config('constants.challenge_requirement_common.no');
                    break;
                default:
                    $allowSubmitProject = config('constants.challenge_requirement_common.no');
                    break;
            }

            $requirementProgram = config('constants.challenge_requirement_common.no');
            switch ($request->requirement_program) {
                case 'yes':
                    $requirementProgram = config('constants.challenge_requirement_common.yes');
                    break;
                case 'no':
                    $requirementProgram = config('constants.challenge_requirement_common.no');
                    break;
                default:
                    $requirementProgram = config('constants.challenge_requirement_common.no');
                    break;
            }

            $completeEducationProgram = config('constants.challenge_requirement_common.no');
            switch ($request->complete_education_program) {
                case 'yes':
                    $completeEducationProgram = config('constants.challenge_requirement_common.yes');
                    break;
                case 'no':
                    $completeEducationProgram = config('constants.challenge_requirement_common.no');
                    break;
                default:
                    $completeEducationProgram = config('constants.challenge_requirement_common.no');
                    break;
            }

            $completeExperience = config('constants.challenge_requirement_common.no');
            switch ($request->complete_experience) {
                case 'yes':
                    $completeExperience = config('constants.challenge_requirement_common.yes');
                    break;
                case 'no':
                    $completeExperience = config('constants.challenge_requirement_common.no');
                    break;
                default:
                    $completeExperience = config('constants.challenge_requirement_common.no');
                    break;
            }

            $min_rank = $request->min_rank ?? 0;
            $min_points = $request->min_points ?? 0;
            $project_submission_requirement_ids = $request->project_submission_requirement_ids ?? [];
            $max_project_submission = $request->max_project_submission ?? 100;
            $max_project_associated = $request->max_project_associated ?? 100;
            $min_experience = $request->min_experience ?? 0;
            $min_imported_badges = $request->min_imported_badges ?? 0;
            $min_achievement_counts = $request->min_achievement_counts ?? 0;
            $additional_requirements = $request->additional_requirements ?? null;

            $challengeRequirement = new ChallengeRequirement();
            $challengeRequirement->challenge_id = $challenge_id;
            $challengeRequirement->min_rank = $min_rank;
            $challengeRequirement->min_points = $min_points;
            $challengeRequirement->project_submission_requirement_ids = $project_submission_requirement_ids;
            $challengeRequirement->max_project_submission = $max_project_submission;
            $challengeRequirement->max_project_associate = $max_project_associated;
            $challengeRequirement->min_experience = $min_experience;
            $challengeRequirement->min_imported_badges = $min_imported_badges;
            $challengeRequirement->min_achievement_counts = $min_achievement_counts;
            $challengeRequirement->allow_submit_project = $allowSubmitProject;
            $challengeRequirement->requirement_program = $requirementProgram;
            $challengeRequirement->complete_education_program = $completeEducationProgram;
            $challengeRequirement->complete_experience = $completeExperience;
            $challengeRequirement->additional_requirements = $additional_requirements;
            $challengeRequirement->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateChallengeRequirement($request, $challenge_id)
    {
        try {
            $challengeRequirement = ChallengeRequirement::where('challenge_id', $challenge_id)->first();
            $allowSubmitProject = config('constants.challenge_requirement_common.no');
            switch ($request->allow_submit_project) {
                case 'yes':
                    $allowSubmitProject = config('constants.challenge_requirement_common.yes');
                    break;
                case 'no':
                    $allowSubmitProject = config('constants.challenge_requirement_common.no');
                    break;
                default:
                    $allowSubmitProject = config('constants.challenge_requirement_common.yes');
                    break;
            }

            $requirementProgram = config('constants.challenge_requirement_common.no');
            switch ($request->requirement_program) {
                case 'yes':
                    $requirementProgram = config('constants.challenge_requirement_common.yes');
                    break;
                case 'no':
                    $requirementProgram = config('constants.challenge_requirement_common.no');
                    break;
                default:
                    $requirementProgram = config('constants.challenge_requirement_common.yes');
                    break;
            }

            $completeEducationProgram = config('constants.challenge_requirement_common.no');
            switch ($request->complete_education_program) {
                case 'yes':
                    $completeEducationProgram = config('constants.challenge_requirement_common.yes');
                    break;
                case 'no':
                    $completeEducationProgram = config('constants.challenge_requirement_common.no');
                    break;
                default:
                    $completeEducationProgram = config('constants.challenge_requirement_common.yes');
                    break;
            }

            $completeExperience = config('constants.challenge_requirement_common.no');
            switch ($request->complete_experience) {
                case 'yes':
                    $completeExperience = config('constants.challenge_requirement_common.yes');
                    break;
                case 'no':
                    $completeExperience = config('constants.challenge_requirement_common.no');
                    break;
                default:
                    $completeExperience = config('constants.challenge_requirement_common.yes');
                    break;
            }

            if (!$challengeRequirement) {
                $challengeRequirement = new ChallengeRequirement();
                $challengeRequirement->challenge_id = $challenge_id;
                $challengeRequirement->min_rank = $request->min_rank;
                $challengeRequirement->min_points = $request->min_points;
                $challengeRequirement->project_submission_requirement_ids = $request->project_submission_requirement_ids;
                $challengeRequirement->max_project_submission = $request->max_project_submission;
                $challengeRequirement->max_project_associate = $request->max_project_associated;
                $challengeRequirement->min_experience = $request->min_experience;
                $challengeRequirement->min_imported_badges = $request->min_imported_badges;
                $challengeRequirement->min_achievement_counts = $request->min_achievement_counts;
                $challengeRequirement->allow_submit_project = $allowSubmitProject;
                $challengeRequirement->requirement_program = $requirementProgram;
                $challengeRequirement->complete_education_program = $completeEducationProgram;
                $challengeRequirement->complete_experience = $completeExperience;
                $challengeRequirement->additional_requirements = $request->additional_requirements;
                $challengeRequirement->save();

                return true;
            }

            $challengeRequirement->min_rank = $request->has('min_rank') ? $request->min_rank : $challengeRequirement->min_rank;
            $challengeRequirement->min_points = $request->has('min_points') ? $request->min_points : $challengeRequirement->min_points;
            $challengeRequirement->max_project_submission = $request->has('max_project_submission') ? $request->max_project_submission : $challengeRequirement->max_project_submission;
            $challengeRequirement->max_project_associate = $request->has('max_project_associated') ? $request->max_project_associated : $challengeRequirement->max_project_associate;
            $challengeRequirement->min_experience = $request->has('min_experience') ? $request->min_experience : $challengeRequirement->min_experience;
            $challengeRequirement->min_imported_badges = $request->has('min_imported_badges') ? $request->min_imported_badges : $challengeRequirement->min_imported_badges;
            $challengeRequirement->min_achievement_counts = $request->has('min_achievement_counts') ? $request->min_achievement_counts : $challengeRequirement->min_achievement_counts;
            $challengeRequirement->allow_submit_project = $request->has('allow_submit_project') ? $allowSubmitProject : $challengeRequirement->allow_submit_project;
            $challengeRequirement->requirement_program = $request->has('requirement_program') ? $requirementProgram : $challengeRequirement->requirement_program;
            $challengeRequirement->complete_education_program = $request->has('complete_education_program') ? $completeEducationProgram : $challengeRequirement->complete_education_program;
            $challengeRequirement->complete_experience = $request->has('complete_experience') ? $completeExperience : $challengeRequirement->complete_experience;
            $challengeRequirement->project_submission_requirement_ids = $request->has('project_submission_requirement_ids') ? $request->project_submission_requirement_ids : $challengeRequirement->project_submission_requirement_ids;
            $challengeRequirement->additional_requirements = $request->has('additional_requirements') ? $request->additional_requirements : $challengeRequirement->additional_requirements;
            $challengeRequirement->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function cloneChallengeRequirement($originalChallengeChallengeRequirements, $clonedChallengeId)
    {
        try {
            if ($originalChallengeChallengeRequirements) {
                $cloneChallengeRequirement = $originalChallengeChallengeRequirements->replicate();
                $cloneChallengeRequirement->challenge_id = $clonedChallengeId;
                $cloneChallengeRequirement->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
