<?php

namespace App\Repositories\Api\Explore;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengeService;
use App\Services\Public\FeaturedModuleService;
use App\Services\Public\LabService;
use App\Services\Public\LabSocialActivitiesService;
use App\Services\SkillService;
use App\Services\UserService;
use App\Services\UserSkillsService;
use App\Services\UserTagsService;
use Illuminate\Support\Facades\DB;

class ExploreRepository implements ExploreInterface
{
    private $labService;
    private $userService;
    private $userSkillsService;
    private $userTagsService;
    private $labSocialActivitiesService;
    private $challengeService;

    private $skillsService;

    private $featuredModuleService;

    public function __construct(FeaturedModuleService $featuredModuleService, LabSocialActivitiesService $labSocialActivitiesService, UserService $userService, LabService $labService, ChallengeService $challengeService, UserSkillsService $userSkillsService, UserTagsService $userTagsService, SkillService $skillsService)
    {
        $this->labSocialActivitiesService = $labSocialActivitiesService;
        $this->userService = $userService;
        $this->labService = $labService;
        $this->userSkillsService = $userSkillsService;
        $this->userTagsService = $userTagsService;
        $this->challengeService = $challengeService;
        $this->skillsService = $skillsService;
        $this->featuredModuleService = $featuredModuleService;
    }

    public function recommendedLabsAndChallenges()
    {
        try {
            $usersSkills = $this->userSkillsService->getUserSkills();
            $getUsersTags = $this->userTagsService->getMyTags();
            $response['labs'] = $this->labService->getLabsBasedOnSKillsAndTags($usersSkills, $getUsersTags);
            $response['challenge'] = $this->challengeService->getChallengeBasedOnSkillsAndTags($usersSkills, $getUsersTags);

            return $response;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
        }
    }

    public function getFeaturedModule()
    {
        try {
            $getFeaturedLabs = $this->featuredModuleService->getFeaturedModule();
            if (!empty($getFeaturedLabs)) {
                return $getFeaturedLabs;
            }

            return false;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function recommendedSkills($getUserSkills)
    {
        try {
            return $this->skillsService->recommendSkills($getUserSkills);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function trendingJobs()
    {
        try {
            $response['labs'] = $this->labService->getTrendingLab();
            $response['challenge'] = $this->challengeService->getTrendingChallenge();

            return $response;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
