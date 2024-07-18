<?php

namespace App\Traits\Maestro\Explore;

use App\Services\Maestro\Explore\ExploreService;
use App\Services\Maestro\RoleAndPermission\RoleAndPermissionService;
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
            dd($e);

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
            return false;
        }
    }

    private function getExploreData()
    {
        try {
            $orgs = ExploreService::getExploreData();
            if ($orgs) {
                return $orgs;
            }

            return false;
        } catch (Exception $e) {
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
            return false;
        }
    }
}
