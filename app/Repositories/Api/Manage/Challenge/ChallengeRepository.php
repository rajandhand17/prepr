<?php

namespace App\Repositories\Api\Manage\Challenge;

use App\Services\Manage\ChallengeAchievementService;
use App\Services\Manage\ChallengeAssessmentCriteriaService;
use App\Services\Manage\ChallengeAssessmentService;
use App\Services\Manage\ChallengeProjectTemplateService;
use App\Services\Manage\ChallengeRequirementService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ChallengeSkillsGroupsStackService;
use App\Services\Manage\ChallengeSponsorService;
use App\Services\Manage\ChallengeTagsGroupsService;
use Exception;

class ChallengeRepository implements ChallengeInterface
{
    private $challengeService;
    private $challengeAchievementService;
    private $challengeSponsorService;
    private $challengeSkillsGroupsStackService;
    private $challengeTagsGroupsService;
    private $challengeRequirementService;
    private $challengeAssessmentCriteriaService;
    private $challengeProjectTemplateService;
    private $challengeAssessmentService;

    public function __construct(ChallengeService $challengeService, ChallengeAchievementService $challengeAchievementService, ChallengeSponsorService $challengeSponsorService, ChallengeSkillsGroupsStackService $challengeSkillsGroupsStackService, ChallengeTagsGroupsService $challengeTagsGroupsService, ChallengeRequirementService $challengeRequirementService, ChallengeAssessmentCriteriaService $challengeAssessmentCriteriaService, ChallengeProjectTemplateService $challengeProjectTemplateService, ChallengeAssessmentService $challengeAssessmentService)
    {
        $this->challengeService = $challengeService;
        $this->challengeAchievementService = $challengeAchievementService;
        $this->challengeSponsorService = $challengeSponsorService;
        $this->challengeSkillsGroupsStackService = $challengeSkillsGroupsStackService;
        $this->challengeTagsGroupsService = $challengeTagsGroupsService;
        $this->challengeRequirementService = $challengeRequirementService;
        $this->challengeAssessmentCriteriaService = $challengeAssessmentCriteriaService;
        $this->challengeProjectTemplateService = $challengeProjectTemplateService;
        $this->challengeAssessmentService = $challengeAssessmentService;
    }

    public function uploadChallengeCoverImage($image)
    {
        try {
            return $this->challengeService->uploadChallengeCoverImage($image);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createChallenge($request, $upload_cover_image, $upload_achievement_image)
    {
        try {
            $createChallenge = $this->challengeService->createChallenge($request, $upload_cover_image);
            $createChallengeAchievement = $this->challengeAchievementService->createChallengeAchievement($request, $createChallenge->id, $upload_achievement_image);
            $createChallengeSponsor = $this->challengeSponsorService->createChallengeSponsor($request, $createChallenge->id);
            $createChallengeSkillsGroupsStack = $this->challengeSkillsGroupsStackService->createChallengeSkillsGroupsStack($request, $createChallenge->id);
            $createChallengeTagsGroups = $this->challengeTagsGroupsService->createChallengeTagsGroups($request, $createChallenge->id);
            $createChallengeRequirement = $this->challengeRequirementService->createChallengeRequirement($request, $createChallenge->id);
            $createChallengeAssessmentCriteria = $this->challengeAssessmentCriteriaService->createChallengeAssessmentCriteria($request, $createChallenge->id);
            $createChallengeAssessment = $this->challengeAssessmentService->createChallengeAssessment($request, $createChallenge->id);
            $createChallengeProjectTemplate = $this->challengeProjectTemplateService->createChallengeProjectTemplate($request, $createChallenge->id);

            dd($createChallenge, 'In Repository');
        } catch (Exception $th) {
            dd($th, 'In Repository');

            return false;
        }
    }
}
