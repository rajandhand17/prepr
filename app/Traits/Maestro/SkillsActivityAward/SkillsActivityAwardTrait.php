<?php

namespace App\Traits\Maestro\SkillsActivityAward;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\SkillsActivityAwardService;
use Exception;

trait SkillsActivityAwardTrait
{
    private function createSkillsActivityAward($request)
    {
        try {
            if (SkillsActivityAwardService::createSkillsActivityAward($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function updateSkillsActivityAwardById($id, $request)
    {
        try {
            if (SkillsActivityAwardService::updateSkillsActivityAwardById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function deleteSkillsActivityAwardById($id)
    {
        try {
            if (SkillsActivityAwardService::deleteSkillsActivityAward($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getSkillsActivityAward()
    {
        try {
            $SkillsActivityAward = SkillsActivityAwardService::getSkillsActivityAward();
            if ($SkillsActivityAward) {
                return $SkillsActivityAward;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
