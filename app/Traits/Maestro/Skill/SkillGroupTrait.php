<?php

namespace App\Traits\Maestro\Skill;

use App\Services\Maestro\SkillGroupService;
use Exception;

trait SkillGroupTrait
{
    private function createSkillGroup($request)
    {
        try {
            if (SkillGroupService::createSkillGroup($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            dd($e);

            return false;
        }
    }

    private function getSkillGroupById($id)
    {
        try {
            return SkillGroupService::getSkillGroupById($id);
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateSkillGroupById($id, $request)
    {
        try {
            if (SkillGroupService::updateSkillGroupById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteSkillGroupById($id)
    {
        try {
            if (SkillGroupService::deleteSkillGroupById($id)) {
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
            $skills = SkillGroupService::getSkills();
            if ($skills) {
                return $skills;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
