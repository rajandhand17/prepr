<?php

namespace App\Traits\Maestro\Master;

use App\Services\Maestro\CategoryService;
use App\Services\Maestro\ChallengeService;
use App\Services\Maestro\DurationService;
use App\Services\Maestro\LabService;
use App\Services\Maestro\LevelsService;
use App\Services\Maestro\OrganizationService;
use App\Services\Maestro\RankService;
use App\Services\Maestro\ResourceModuleService;
use App\Services\Maestro\SkillService;
use App\Services\Maestro\UserService;
use App\Helpers\UtilityHelper;
use Exception;

trait MasterTrait
{
    private function getOrganizationsById($request)
    {
        try {
            $organizations = OrganizationService::getOrganizationsById($request);
            if ($organizations) {
                return $organizations;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getCategoriesById($request)
    {
        try {
            $categories = CategoryService::getCategoriesByLanguageId($request);
            if ($categories) {
                return $categories;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getMinRanksById($request)
    {
        try {
            $ranks = RankService::getMinRanksById($request);
            if ($ranks) {
                return $ranks;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getSkillsById($request)
    {
        try {
            $skills = SkillService::getSkillsByLanguageId($request);
            if ($skills) {
                return $skills;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getUsersById($request)
    {
        try {
            $users = UserService::getUsersById($request);
            if ($users) {
                return $users;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getLabsById($request)
    {
        try {
            $labs = LabService::getLabsByLanguageId($request);
            if ($labs) {
                return $labs;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getResourceModulesById($request)
    {
        try {
            $resourceModules = ResourceModuleService::getResourceModulesById($request);
            if ($resourceModules) {
                return $resourceModules;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getLevelsById($request)
    {
        try {
            $labs = LevelsService::getLevelsById($request);
            if ($labs) {
                return $labs;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getDurationsById($request)
    {
        try {
            $labs = DurationService::getDurationsById($request);
            if ($labs) {
                return $labs;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getUsersEmail($request)
    {
        try {
            $users = UserService::getUsersEmail($request);
            if ($users) {
                return $users;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getAllChallenges($request)
    {
        try {
            $challenges = ChallengeService::getChallenges($request);
            if ($challenges) {
                return $challenges;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function getAllLabsById($request)
    {
        try {
            $labs = LabService::getLabs($request);
            if ($labs) {
                return $labs;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
