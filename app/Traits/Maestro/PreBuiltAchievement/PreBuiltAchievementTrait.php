<?php

namespace App\Traits\Maestro\PreBuiltAchievement;

use App\Services\Maestro\PreBuiltAchievementService;
use Exception;

trait PreBuiltAchievementTrait
{
    private function getPreBuiltAchievement()
    {
        try {
            $achievement = PreBuiltAchievementService::getPreBuiltAchievement();
            if ($achievement) {
                return $achievement;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function storeUpdatePreBuiltAchievement($request, $id, $moduleMode)
    {
        try {
            if (PreBuiltAchievementService::storeUpdatePreBuiltAchievement($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function findPreBuiltAchievement($id)
    {
        try {
            $achievement = PreBuiltAchievementService::findPreBuiltAchievement($id);
            if ($achievement) {
                return $achievement;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deletePreBuiltAchievement($achievement)
    {
        try {
            if (PreBuiltAchievementService::deletePreBuiltAchievement($achievement)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
