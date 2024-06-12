<?php

namespace App\Traits\Maestro\Skill;

use App\Services\Maestro\Skill\SkillService;
use App\Services\Maestro\RoleAndPermission\RoleAndPermissionService;
use App\Services\Maestro\Skill\SkillStackService;
use Exception;

trait SkillStackTrait
{
    private function createSkillStack($request)
    {
        try {
            if(SkillStackService::createSkillStack($request)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }
    private function getSkillStackById($id)
    {
        try {
            return SkillStackService::getSkillStackById($id);
        } catch (Exception $e) {
            return false;
        }
    }
    private function updateSkillStackById($id,$request)
    {
        try {
            if(SkillStackService::updateSkillStackById($id,$request)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    private function deleteSkillStackById($id)
    {
        try {
            if(SkillStackService::deleteSkillStackById($id)){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    private function getSkills()
    {
        try {
            $skills = SkillStackService::getSkills();
            if($skills){
                return $skills;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
