<?php

namespace App\Traits\Maestro\ChallengeTemplate;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeTemplate;
use App\Services\Maestro\ChallengeAchievementService;
use App\Services\Maestro\ChallengeRequirementService;
use App\Services\Maestro\ChallengeService;
use App\Services\Maestro\ChallengeSkillsGroupsStackService;
use App\Services\Maestro\ChallengeTemplateService;
use App\Services\Maestro\ChallengeTimelineService;
use App\Services\Maestro\ComponentAssociationService;
use App\Services\Maestro\ChallengeTemplateAchievementService;
use App\Services\Maestro\ChallengeTemplateSkillsGroupsStackService;
use App\Services\Maestro\ChallengeTemplateSponsorService;
use App\Services\Maestro\ChallengeTemplateRequirementService;
use App\Services\Maestro\ChallengeTemplateAssessmentService;
use App\Services\Maestro\ChallengeTemplateAssessmentCriteriaService;
use App\Services\Maestro\ChallengeTemplateProjectTemplateService;
use App\Services\Maestro\ChallengeTemplateTimelinesService;
use App\Services\Maestro\ChallengeTemplateCustomTimelinesService;
use App\Services\Maestro\ChallengeTemplateExternalLinkService;
use Illuminate\Support\Facades\DB;

trait ChallengeTemplateTrait
{
    public function getChallengeTemplate()
    {
        try {
            $challengeTemplate = $this->challengeTemplateService->getChallengesTemplate();
            if ($challengeTemplate) {
                return $challengeTemplate;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteChallengeTemplateById($id)
    {
        try {
            $getChallengeTemplate = $this->challengeTemplateService->getChallengeTemplateBasedOnId($id);
            $challengeService = $this->challengeTemplateService->deleteChallengeTemplate($getChallengeTemplate->slug, $id);
            if ($challengeService) {
                return $challengeService;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getChallengeTemplateById($id)
    {
        try {
            $challenges = $this->challengeTemplateService->getChallengeTemplateBasedOnId($id);
            if ($challenges) {
                return $challenges;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getChallengeBasedOnSlug($slug)
    {
        try {
            return $this->challengeService->getChallengeBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getCheckChallengeUuid($uuid)
    {
        try {
            return $this->challengeTemplateService->getChallengeTemplateBasedOnUuid($uuid);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createChallengeTemplate($challengeId)
    {
        try {
            $createChallengeTemplate = DB::transaction(function () use ($challengeId) {
                $addChallengeTemplate = ChallengeTemplateService::createChallengeTemplate($challengeId);
                $challengeTemplateParticipationAchievement =ChallengeTemplateAchievementService::addChallengeTemplateAchievement($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateSkills = ChallengeTemplateSkillsGroupsStackService::addChallengeTemplateSkills($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateSponsor = ChallengeTemplateSponsorService::addChallengeTemplateSponsor($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateRequirement = ChallengeTemplateRequirementService::addChallengeTemplateRequirement($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateAssessment = ChallengeTemplateAssessmentService::addChallengeTemplateAssessment($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateAssessmentCriteria = ChallengeTemplateAssessmentCriteriaService::addChallengeTemplateAssessmentCriteria($challengeId, $addChallengeTemplate->id, $addChallengeTemplateAssessment);
                $addChallengeTemplateProjectTemplate = ChallengeTemplateProjectTemplateService::addChallengeTemplateProjectTemplate($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateTimelines = ChallengeTemplateTimelinesService::addChallengeTemplateTimelines($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateCustomTimelines = ChallengeTemplateCustomTimelinesService::addChallengeTemplateCustomTimeLines($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateExternalLink = ChallengeTemplateExternalLinkService::addChallengeTemplateExternalLink($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateComponentAssociation = ChallengeTemplateService::addChallengeTemplateComponentAssociation($challengeId, $addChallengeTemplate->id);
                $updateChallenge = ChallengeService::updatePreBuilt($challengeId, '1');

                return [
                    'addChallengeTemplate'                         => $addChallengeTemplate,
                    'challengeTemplateParticipationAchievement'    => $challengeTemplateParticipationAchievement,
                    'addChallengeTemplateSkills'                   => $addChallengeTemplateSkills,
                    'addChallengeTemplateSponsor'                  => $addChallengeTemplateSponsor,
                    'addChallengeTemplateRequirement'              => $addChallengeTemplateRequirement,
                    'addChallengeTemplateAssessment'               => $addChallengeTemplateAssessment,
                    'addChallengeTemplateAssessmentCriteria'       => $addChallengeTemplateAssessmentCriteria,
                    'addChallengeTemplateProjectTemplate'          => $addChallengeTemplateProjectTemplate,
                    'addChallengeTemplateTimelines'                => $addChallengeTemplateTimelines,
                    'addChallengeTemplateCustomTimelines'          => $addChallengeTemplateCustomTimelines,
                    'addChallengeTemplateExternalLink'             => $addChallengeTemplateExternalLink,
                    'addChallengeTemplateComponentAssociation'     => $addChallengeTemplateComponentAssociation,
                    'updateChallenge'                              => $updateChallenge,
                ];
            });

            if ($createChallengeTemplate['addChallengeTemplate'] &&
                $createChallengeTemplate['challengeTemplateParticipationAchievement'] &&
                $createChallengeTemplate['addChallengeTemplateSkills'] &&
                $createChallengeTemplate['addChallengeTemplateSponsor'] &&
                $createChallengeTemplate['addChallengeTemplateRequirement'] &&
                $createChallengeTemplate['addChallengeTemplateAssessment'] &&
                $createChallengeTemplate['addChallengeTemplateAssessmentCriteria'] &&
                $createChallengeTemplate['addChallengeTemplateProjectTemplate'] &&
                $createChallengeTemplate['addChallengeTemplateTimelines'] &&
                $createChallengeTemplate['addChallengeTemplateCustomTimelines'] &&
                $createChallengeTemplate['addChallengeTemplateExternalLink'] &&
                $createChallengeTemplate['addChallengeTemplateComponentAssociation'] &&
                $createChallengeTemplate['updateChallenge']
            ) {
                DB::commit();

                return $createChallengeTemplate['addChallengeTemplate'];
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
