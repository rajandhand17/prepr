<?php

namespace App\Repositories\Api\Explore;


use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabSkillsGroupsStackService;
use App\Services\Manage\LabTagsGroupsService;
use App\Services\Public\LabService;
use App\Services\SkillService;
use App\Services\TagService;
use App\Services\UserSkillsService;
use App\Services\UserTagsService;
use DB;

class ExploreRepository implements ExploreInterface
{
    private $labService;
    private $userSkillsService;
    private $userTagsService;

    private $challengeService;
    public function __construct(LabService $labService, ChallengeService $challengeService,UserSkillsService $userSkillsService,UserTagsService $userTagsService)
    {
        $this->labService =$labService;
        $this->userSkillsService=$userSkillsService;
        $this->userTagsService=$userTagsService;
        $this->challengeService=$challengeService;
    }
    public function recommended($request)
    {
        try {
            //DB::transaction();
            $usersSkills=$this->userSkillsService->getUserSkills();
            $getUsersTags=$this->userTagsService->getMyTags();
            $getLabs=$this->labService->getLabsBasedOnSKillsAndTags($usersSkills,$getUsersTags);
            $getChallenges=$this->challengeService->getChallengeBasedOnSkillsAndTags($usersSkills,$getUsersTags);
            return $getLabs;
        }catch (\Exception $e){
            return false;
        }
    }

}
