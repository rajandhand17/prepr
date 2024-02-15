<?php

namespace App\Repositories\Api\Public\Skill;

use App\Services\SkillService;
use App\Services\UserSkillsService;

class SkillRepository implements SkillInterface
{
    private $skillsService;

    private $userSkillsService;
    public function __construct(SkillService $skillsService,UserSkillsService $userSkillsService){
        $this->skillsService = $skillsService;
        $this->userSkillsService = $userSkillsService;
    }
    public function index($language,$search,$skillId){
        try {
            return $this->skillsService->getSkills($language,$search,$skillId);
        }catch(\Exception $e) {
            return false;
        }
    }
    public function getMySkills($language, $search)
    {
        try {
            return $this->userSkillsService->getMySkills($language, $search);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function getSkillBasedOnId($skillId)
    {
        try {
            return $this->skillsService->getSkillBasedOnId($skillId);
        } catch (\Exception $e) {
            return false;
        }
    }
}
