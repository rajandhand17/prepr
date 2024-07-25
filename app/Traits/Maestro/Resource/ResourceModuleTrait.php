<?php

namespace App\Traits\Maestro\Resource;

use App\Services\Maestro\ResourceModuleService;
use Exception;

trait ResourceModuleTrait
{
    private function getResourceModuleList()
    {
        try {
            $resourceModules = ResourceModuleService::getResourceModuleList();
            if ($resourceModules) {
                return $resourceModules;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function createAndUpdateResourceModule($request,$action,$id)
    {
        try {
            if (ResourceModuleService::createAndUpdateResourceModule($request,$action,$id)) {
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
}
