<?php

namespace App\Traits\Maestro\Explore;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\ExploreService;
use App\Services\Maestro\RoleAndPermissionService;
use Exception;

trait ExploreTrait
{
    private function updateExploreDataById($id, $request)
    {
        try {
            if (ExploreService::updateExploreDataById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function deleteExploreDataById($id)
    {
        try {
            if (ExploreService::deleteExploreData($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getExploreData()
    {
        try {
            $data = ExploreService::getExploreData();
            if ($data) {
                return $data;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getAllRoles()
    {
        try {
            $roles = RoleAndPermissionService::getAllRoles();
            if ($roles) {
                return $roles;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function insertExploreDatas($request)
    {
        try {
            $data = ExploreService::insertExploreDatas($request);
            if ($data) {
                return $data;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
