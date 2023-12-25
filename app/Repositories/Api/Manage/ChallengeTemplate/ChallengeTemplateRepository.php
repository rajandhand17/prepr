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

    public function createTemplateChallenge($challengeId, $organization)
    {
        try {
            $createTemplateChallenge = DB::transaction(function () use ($challengeId, $organization) {
                $createTemplateChallenge = $this->challengeTemplateService->createTemplateChallenge($challengeId, $organization);
                $createTemplateChallengeParticipationAchievement = $this->challengeTemplateAchievementService->createChallengeTemplateAchievement($challengeId, $createTemplateChallenge->id);
                $createTemplateChallengeSkills = $this->challengeTemplateSkillsGroupsStackService->createChallengeTemplateSkills($challengeId, $createTemplateChallenge->id);
                $createTemplateChallengeSponsor = $this->challengeTemplateSponsorService->createChallengeTemplateSponsor($challengeId, $createTemplateChallenge->id);
                $createTemplateChallengeTags = $this->challengeTemplateTagsGroupsService->createChallengeTemplateTagsGroups($challengeId, $createTemplateChallenge->id);
                $createTemplateChallengeRequirement = $this->challengeTemplateRequirementService->createChallengeTemplateRequirement($challengeId, $createTemplateChallenge->id);
                $createTemplateChallengeAssessmentCriteria = $this->challengeTemplateAssessmentCriteriaService->createChallengeTemplateAssessmentCriteria($challengeId, $createTemplateChallenge->id);
                $createTemplateChallengeAssessment = $this->challengeTemplateAssessmentService->createChallengeTemplateAssessment($challengeId, $createTemplateChallenge->id);
                $createTemplateChallengeProjectTemplate = $this->challengeTemplateProjectTemplateService->createChallengeTemplateProjectTemplate($challengeId, $createTemplateChallenge->id);
                $createChallengeTimelines = $this->challengeTemplateTimelinesService->createChallengeTemplateTimelines($challengeId, $createTemplateChallenge->id);
                $createChallengeCustomTimelines = $this->challengeTemplateCustomTimelinesService->createChallengeTemplateCustomTimeLines($challengeId, $createTemplateChallenge->id);
                $createChallengeExternalLink = $this->challengeTemplateExternalLinkService->createChallengeTemplateExternalLink($challengeId, $createTemplateChallenge->id);

                return [
                    'createTemplateChallenge'                           => $createTemplateChallenge,
                    'createTemplateChallengeParticipationAchievement'   => $createTemplateChallengeParticipationAchievement,
                    'createTemplateChallengeSkills'                     => $createTemplateChallengeSkills,
                    'createTemplateChallengeSponsor'                    => $createTemplateChallengeSponsor,
                    'createTemplateChallengeTags'                       => $createTemplateChallengeTags,
                    'createTemplateChallengeRequirement'                => $createTemplateChallengeRequirement,
                    'createTemplateChallengeAssessmentCriteria'         => $createTemplateChallengeAssessmentCriteria,
                    'createTemplateChallengeAssessment'                 => $createTemplateChallengeAssessment,
                    'createTemplateChallengeProjectTemplate'            => $createTemplateChallengeProjectTemplate,
                    'createChallengeTimelines'                          => $createChallengeTimelines,
                    'createChallengeCustomTimelines'                    => $createChallengeCustomTimelines,
                    'createChallengeExternalLink'                       => $createChallengeExternalLink,
                ];
            });

            if (
                $createTemplateChallenge['createTemplateChallenge'] &&
                $createTemplateChallenge['createTemplateChallengeParticipationAchievement'] &&
                $createTemplateChallenge['createTemplateChallengeSkills'] &&
                $createTemplateChallenge['createTemplateChallengeSponsor'] &&
                $createTemplateChallenge['createTemplateChallengeTags'] &&
                $createTemplateChallenge['createTemplateChallengeRequirement'] &&
                $createTemplateChallenge['createTemplateChallengeAssessmentCriteria'] &&
                $createTemplateChallenge['createTemplateChallengeAssessment'] &&
                $createTemplateChallenge['createTemplateChallengeProjectTemplate'] &&
                $createTemplateChallenge['createChallengeTimelines'] &&
                $createTemplateChallenge['createChallengeCustomTimelines'] &&
                $createTemplateChallenge['createChallengeExternalLink']
            ) {
                DB::commit();

                return $createTemplateChallenge['createTemplateChallenge'];
            }

            DB::rollback();

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
