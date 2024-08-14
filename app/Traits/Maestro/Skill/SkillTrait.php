<?php

namespace App\Traits\Maestro\Skill;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\SkillService;
use Exception;

trait SkillTrait
{
    private function createSkill($request)
    {
        try {
            if (SkillService::createSkill($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getSkillById($id)
    {
        try {
            return SkillService::getSkillById($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function updateSkillById($id, $request)
    {
        try {
            if (SkillService::updateSkillById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function deleteSkillById($id)
    {
        try {
            if (SkillService::deleteSkill($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getSkills()
    {
        try {
            $skills = SkillService::getSkills();
            if ($skills) {
                return $skills;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getAjaxAllSkills($request)
    {
        try {
            $skills = SkillService::getAjaxAllSkills($request);
            if ($skills) {
                return $skills;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
