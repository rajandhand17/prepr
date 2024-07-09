<?php

namespace App\Listeners\ChallengeTemplate;

use App\Events\ChallengeTemplate\DeleteChallengeTemplateAssociatedData;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ChallengeTemplateAchievementService;
use App\Services\Manage\ChallengeTemplateAssessmentCriteriaService;
use App\Services\Manage\ChallengeTemplateAssessmentService;
use App\Services\Manage\ChallengeTemplateCustomTimelinesService;
use App\Services\Manage\ChallengeTemplateExternalLinkService;
use App\Services\Manage\ChallengeTemplateProjectTemplateService;
use App\Services\Manage\ChallengeTemplateRequirementService;
use App\Services\Manage\ChallengeTemplateSkillsGroupsStackService;
use App\Services\Manage\ChallengeTemplateSponsorService;
use App\Services\Manage\ChallengeTemplateTimelinesService;
use Exception;

class HandleDeleteChallengeTemplateAssociatedData
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DeleteChallengeTemplateAssociatedData $event)
    {
        try {
            $challengeTemplateId = $event->challengeTemplateId;
            $challengeTemplateAchievement = ChallengeTemplateAchievementService::deleteChallengeTemplateAchievement($challengeTemplateId);
            if (!$challengeTemplateAchievement) {
                return false;
            }

            $challengeTemplateSkillsGroupsStack = ChallengeTemplateSkillsGroupsStackService::deleteChallengeTemplateSkillsGroupsStacks($challengeTemplateId);
            if (!$challengeTemplateSkillsGroupsStack) {
                return false;
            }

            $challengeTemplateSponsor = ChallengeTemplateSponsorService::deleteChallengeTemplateSponsor($challengeTemplateId);
            if (!$challengeTemplateSponsor) {
                return false;
            }

            $challengeTemplateRequirement = ChallengeTemplateRequirementService::deleteChallengeTemplateRequirement($challengeTemplateId);
            if (!$challengeTemplateRequirement) {
                return false;
            }

            $challengeTemplateAssessmentCriteria = ChallengeTemplateAssessmentCriteriaService::deleteChallengeTemplateAssessmentCriteria($challengeTemplateId);
            if (!$challengeTemplateAssessmentCriteria) {
                return false;
            }

            $challengeTemplateAssessment = ChallengeTemplateAssessmentService::deleteChallengeTemplateAssessment($challengeTemplateId);
            if (!$challengeTemplateAssessment) {
                return false;
            }

            $challengeTemplateProjectTemplate = ChallengeTemplateProjectTemplateService::deleteChallengeTemplateProjectTemplate($challengeTemplateId);
            if (!$challengeTemplateProjectTemplate) {
                return false;
            }

            $challengeTemplateTimelines = ChallengeTemplateTimelinesService::deleteChallengeTemplateTimelines($challengeTemplateId);
            if (!$challengeTemplateTimelines) {
                return false;
            }

            $challengeTemplateCustomTimelines = ChallengeTemplateCustomTimelinesService::deleteChallengeTemplateCustomTimelines($challengeTemplateId);
            if (!$challengeTemplateCustomTimelines) {
                return false;
            }

            $challengeTemplateExternalLink = ChallengeTemplateExternalLinkService::deleteChallengeTemplateExternalLink($challengeTemplateId);
            if (!$challengeTemplateExternalLink) {
                return false;
            }

            $challengeTemplateUpdatePreBuilt = ChallengeService::challengeTemplateUpdatePreBuilt($challengeTemplateId);
            if (!$challengeTemplateUpdatePreBuilt) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
