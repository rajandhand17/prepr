<?php

namespace App\Repositories\Api\Explore;


use App\Services\Manage\ChallengeService;
use App\Services\Manage\LabSkillsGroupsStackService;
use App\Services\Manage\LabTagsGroupsService;
use App\Services\Public\LabService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\TagService;
use App\Services\UserSkillsService;
use App\Services\UserTagsService;
use \Illuminate\Support\Facades\DB;

class ExploreRepository implements ExploreInterface
{
    private $labService;
    private $userSkillsService;
    private $userTagsService;

    private $skillGroupsService;
    private $challengeService;
    public function __construct(LabService $labService,SkillGroupService $skillGroupsService,ChallengeService $challengeService,UserSkillsService $userSkillsService,UserTagsService $userTagsService)
    {
        $this->labService =$labService;
        $this->userSkillsService=$userSkillsService;
        $this->userTagsService=$userTagsService;
        $this->challengeService=$challengeService;
        $this->skillGroupsService=$skillGroupsService;
    }
    public function recommended($request)
    {
        try {
            DB::beginTransaction();
            $usersSkills=$this->userSkillsService->getUserSkills();
            $getUsersTags=$this->userTagsService->getMyTags();
            $response['labs']     =$this->labService->getLabsBasedOnSKillsAndTags($usersSkills,$getUsersTags);
            $response['challenge']=$this->challengeService->getChallengeBasedOnSkillsAndTags($usersSkills,$getUsersTags);
            DB::commit();
            return $response;
        }catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }

    public function recommendSKills(){
        try {
            $userSkills=$this->userSkillsService->getUserSkills();
            $recommendedSkills=$this->skillGroupsService->recommendedSkillsGroup($userSkills);
        }catch (\Exception $e){
            return false;
        }
    }
}
