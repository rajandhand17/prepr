<?php

namespace App\Repositories\Api\Manage\ChallengeTemplate;

use App\Services\Manage\ChallengeTemplateAchievementService;
use App\Services\Manage\ChallengeTemplateAssessmentCriteriaService;
use App\Services\Manage\ChallengeTemplateAssessmentService;
use App\Services\Manage\ChallengeTemplateCustomTimelinesService;
use App\Services\Manage\ChallengeTemplateExternalLinkService;
use App\Services\Manage\ChallengeTemplateProjectTemplateService;
use App\Services\Manage\ChallengeTemplateRequirementService;
use App\Services\Manage\ChallengeTemplateService;
use App\Services\Manage\ChallengeTemplateSkillsGroupsStackService;
use App\Services\Manage\ChallengeTemplateSponsorService;
use App\Services\Manage\ChallengeTemplateTagsGroupsService;
use App\Services\Manage\ChallengeTemplateTimelinesService;
use Exception;
use Illuminate\Support\Facades\DB;

class ChallengeTemplateRepository implements ChallengeTemplateInterface
{
    private $challengeTemplateService;

    private $challengeTemplateAchievementService;

    private $challengeTemplateSkillsGroupsStackService;

    private $challengeTemplateSponsorService;

    private $challengeTemplateTagsGroupsService;

    private $challengeTemplateRequirementService;

    private $challengeTemplateAssessmentCriteriaService;

    private $challengeTemplateAssessmentService;

    private $challengeTemplateProjectTemplateService;

    private $challengeTemplateTimelinesService;

    private $challengeTemplateCustomTimelinesService;

    private $challengeTemplateExternalLinkService;

    public function __construct(ChallengeTemplateExternalLinkService $challengeTemplateExternalLinkService, ChallengeTemplateCustomTimelinesService $challengeTemplateCustomTimelinesService, ChallengeTemplateTimelinesService $challengeTemplateTimelinesService, ChallengeTemplateProjectTemplateService $challengeTemplateProjectTemplateService, ChallengeTemplateAssessmentService $challengeTemplateAssessmentService, ChallengeTemplateAssessmentCriteriaService $challengeTemplateAssessmentCriteriaService, ChallengeTemplateRequirementService $challengeTemplateRequirementService, ChallengeTemplateTagsGroupsService $challengeTemplateTagsGroupsService, ChallengeTemplateSponsorService $challengeTemplateSponsorService, ChallengeTemplateSkillsGroupsStackService $challengeTemplateSkillsGroupsStackService, ChallengeTemplateService $challengeTemplateService, ChallengeTemplateAchievementService $challengeTemplateAchievementService)
    {
        $this->challengeTemplateService = $challengeTemplateService;
        $this->challengeTemplateAchievementService = $challengeTemplateAchievementService;
        $this->challengeTemplateSkillsGroupsStackService = $challengeTemplateSkillsGroupsStackService;
        $this->challengeTemplateSponsorService = $challengeTemplateSponsorService;
        $this->challengeTemplateTagsGroupsService = $challengeTemplateTagsGroupsService;
        $this->challengeTemplateRequirementService = $challengeTemplateRequirementService;
        $this->challengeTemplateAssessmentService = $challengeTemplateAssessmentService;
        $this->challengeTemplateProjectTemplateService = $challengeTemplateProjectTemplateService;
        $this->challengeTemplateAssessmentCriteriaService = $challengeTemplateAssessmentCriteriaService;
        $this->challengeTemplateTimelinesService = $challengeTemplateTimelinesService;
        $this->challengeTemplateCustomTimelinesService = $challengeTemplateCustomTimelinesService;
        $this->challengeTemplateExternalLinkService = $challengeTemplateExternalLinkService;
    }

    public function getChallengeTemplateList($request)
    {
        try {
            return $this->challengeTemplateService->getChallengeTemplateList($request);
        } catch (Exception $e) {
            return false;
        }
    }

    public function addChallengeToTemplate($challengeId)
    {
        try {
            $addChallengeToTemplate = DB::transaction(function () use ($challengeId) {
                $addChallengeTemplate = $this->challengeTemplateService->addChallengeTemplate($challengeId);
                $addChallengeTemplateParticipationAchievement = $this->challengeTemplateAchievementService->addChallengeTemplateAchievement($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateSkills = $this->challengeTemplateSkillsGroupsStackService->addChallengeTemplateSkills($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateSponsor = $this->challengeTemplateSponsorService->addChallengeTemplateSponsor($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateTags = $this->challengeTemplateTagsGroupsService->addChallengeTemplateTagsGroups($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateRequirement = $this->challengeTemplateRequirementService->addChallengeTemplateRequirement($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateAssessmentCriteria = $this->challengeTemplateAssessmentCriteriaService->addChallengeTemplateAssessmentCriteria($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateAssessment = $this->challengeTemplateAssessmentService->addChallengeTemplateAssessment($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateProjectTemplate = $this->challengeTemplateProjectTemplateService->addChallengeTemplateProjectTemplate($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateTimelines = $this->challengeTemplateTimelinesService->addChallengeTemplateTimelines($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateCustomTimelines = $this->challengeTemplateCustomTimelinesService->addChallengeTemplateCustomTimeLines($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateExternalLink = $this->challengeTemplateExternalLinkService->addChallengeTemplateExternalLink($challengeId, $addChallengeTemplate->id);

                return [
                    'addChallengeTemplate'                              => $addChallengeTemplate,
                    'addChallengeTemplateParticipationAchievement'      => $addChallengeTemplateParticipationAchievement,
                    'addChallengeTemplateSkills'                        => $addChallengeTemplateSkills,
                    'addChallengeTemplateSponsor'                       => $addChallengeTemplateSponsor,
                    'addChallengeTemplateTags'                          => $addChallengeTemplateTags,
                    'addChallengeTemplateRequirement'                   => $addChallengeTemplateRequirement,
                    'addChallengeTemplateAssessmentCriteria'            => $addChallengeTemplateAssessmentCriteria,
                    'addChallengeTemplateAssessment'                    => $addChallengeTemplateAssessment,
                    'addChallengeTemplateProjectTemplate'               => $addChallengeTemplateProjectTemplate,
                    'addChallengeTemplateTimelines'                     => $addChallengeTemplateTimelines,
                    'addChallengeTemplateCustomTimelines'               => $addChallengeTemplateCustomTimelines,
                    'addChallengeTemplateExternalLink'                  => $addChallengeTemplateExternalLink,
                ];
            });

            if (
                $addChallengeToTemplate['addChallengeTemplate'] &&
                $addChallengeToTemplate['addChallengeTemplateParticipationAchievement'] &&
                $addChallengeToTemplate['addChallengeTemplateSkills'] &&
                $addChallengeToTemplate['addChallengeTemplateSponsor'] &&
                $addChallengeToTemplate['addChallengeTemplateTags'] &&
                $addChallengeToTemplate['addChallengeTemplateRequirement'] &&
                $addChallengeToTemplate['addChallengeTemplateAssessmentCriteria'] &&
                $addChallengeToTemplate['addChallengeTemplateAssessment'] &&
                $addChallengeToTemplate['addChallengeTemplateProjectTemplate'] &&
                $addChallengeToTemplate['addChallengeTemplateTimelines'] &&
                $addChallengeToTemplate['addChallengeTemplateCustomTimelines'] &&
                $addChallengeToTemplate['addChallengeTemplateExternalLink']
            ) {
                DB::commit();

                return $addChallengeToTemplate['addChallengeTemplate'];
            }

            DB::rollback();

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getChallengeTemplateBasedOnSlug($slug)
    {
        try {
            return $this->challengeTemplateService->getChallengeTemplateBasedOnSlug($slug);
        } catch (Exception $e) {
            return false;
        }
    }
}
