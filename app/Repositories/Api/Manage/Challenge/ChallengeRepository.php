<?php

namespace App\Repositories\Api\Manage\Challenge;

use App\Services\Manage\ChallengeAchievementService;
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

    public function __construct(ChallengeService $challengeService, ChallengeAchievementService $challengeAchievementService, ChallengeSponsorService $challengeSponsorService, ChallengeSkillsGroupsStackService $challengeSkillsGroupsStackService, ChallengeTagsGroupsService $challengeTagsGroupsService, ChallengeRequirementService $challengeRequirementService)
    {
        $this->challengeService = $challengeService;
        $this->challengeAchievementService = $challengeAchievementService;
        $this->challengeSponsorService = $challengeSponsorService;
        $this->challengeSkillsGroupsStackService = $challengeSkillsGroupsStackService;
        $this->challengeTagsGroupsService = $challengeTagsGroupsService;
        $this->challengeRequirementService = $challengeRequirementService;
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

            dd($createChallenge, 'In Repository');
        } catch (Exception $th) {
            dd($th, 'In Repository');

            return false;
        }
    }
}
