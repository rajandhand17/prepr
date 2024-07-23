<?php

namespace App\Traits\Maestro\Master;

use App\Services\Maestro\Challenge\ChallengeService;
use App\Services\Maestro\LabService;
use App\Services\Maestro\Master\MasterService;
use Exception;

trait MasterTrait
{
    private function getOrganizationsById($request)
    {
        try {
            $organizations = MasterService::getOrganizationsById($request);
            if ($organizations) {
                return $organizations;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getCategoriesById($request)
    {
        try {
            $categories = MasterService::getCategoriesById($request);
            if ($categories) {
                return $categories;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getMinRanksById($request)
    {
        try {
            $ranks = MasterService::getMinRanksById($request);
            if ($ranks) {
                return $ranks;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getSkillsById($request)
    {
        try {
            $skills = MasterService::getSkillsById($request);
            if ($skills) {
                return $skills;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getUsersById($request)
    {
        try {
            $users = MasterService::getUsersById($request);
            if ($users) {
                return $users;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getLabsById($request)
    {
        try {
            $labs = MasterService::getLabsById($request);
            if ($labs) {
                return $labs;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getResourceModulesById($request)
    {
        try {
            $resourceModules = MasterService::getResourceModulesById($request);
            if ($resourceModules) {
                return $resourceModules;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getLevelsById($request)
    {
        try {
            $labs = MasterService::getLevelsById($request);
            if ($labs) {
                return $labs;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getDurationsById($request)
    {
        try {
            $labs = MasterService::getDurationsById($request);
            if ($labs) {
                return $labs;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getUsersEmail($request)
    {
        try {
            $users = MasterService::getUsersEmail($request);
            if ($users) {
                return $users;
            }

            return false;
        } catch (Exception $e) {
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
            return false;
        }
    }
}
