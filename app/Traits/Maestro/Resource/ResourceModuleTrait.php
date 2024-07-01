<?php

namespace App\Traits\Maestro\Resource;

use App\Services\Maestro\Resource\ResourceModuleService;
use Exception;

trait ResourceModuleTrait
{
    private function getResourceModuleUser()
    {
        try {
            $users = ResourceModuleService::getResourceModuleUser();
            if ($users) {
                return $users;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getResourceModuleOrganization()
    {
        try {
            $organizations = ResourceModuleService::getResourceModuleOrganization();
            if ($organizations) {
                return $organizations;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getResourceModulePrivacy()
    {
        try {
            $privacy = ResourceModuleService::getResourceModulePrivacy();
            if ($privacy) {
                return $privacy;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getLanguage()
    {
        try {
            $language = ResourceModuleService::getLanguage();
            if ($language) {
                return $language;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getResourceModuleList()
    {
        try {
            $sponsorList = ResourceModuleService::getResourceModuleList();
            if ($sponsorList) {
                return $sponsorList;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getResourceModuleStatus()
    {
        try {
            $status = ResourceModuleService::getResourceModuleStatus();
            if ($status) {
                return $status;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function createResourceModule($request)
    {
        try {
            if (ResourceModuleService::createResourceModule($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteResourceModuleById($id)
    {
        try {
            if (ResourceModuleService::deleteResourceModule($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getResourceModuleById($id)
    {
        try {
            return ResourceModuleService::getResourceModuleById($id);
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateResourceModuleById($id, $request)
    {
        try {
            if (ResourceModuleService::updateResourceModuleById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
