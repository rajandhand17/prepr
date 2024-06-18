<?php

namespace App\Repositories\Api\Public\Skill;

use App\Services\SkillService;
use App\Services\UserSkillsService;

class SkillRepository implements SkillInterface
{
    private $skillsService;

    private $userSkillsService;

    public function __construct(SkillService $skillsService, UserSkillsService $userSkillsService)
    {
        $this->skillsService = $skillsService;
        $this->userSkillsService = $userSkillsService;
    }

    public function index($language, $search, $sortBy, $skillId)
    {
        try {
            if ($skillId !== null) {
                return $this->skillsService->getSkillBasedOnId($skillId);
            } else {
                $pagination = true;

                return  $this->skillsService->getSkills($language, $search, $sortBy, $skillId, $pagination);
            }
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getMySkills($language, $search, $pinned,$sortBy)
    {
        try {
            return $this->userSkillsService->getMySkills($language, $search,$pinned, $sortBy);
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

    public function addSkills($request)
    {
        try {
            return $this->userSkillsService->addSingleSkill($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addSkillPinned($request)
    {
        try {
            return $this->userSkillsService->addSkillPinned($request);
        } catch (\Exception $e) {
            return false;
        }
    }
}
