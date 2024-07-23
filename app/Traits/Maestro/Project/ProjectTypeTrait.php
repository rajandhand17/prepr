<?php

namespace App\Traits\Maestro\Project;

use App\Services\Maestro\ProjectTypeService;
use Exception;

trait ProjectTypeTrait
{
    private function getProjectType()
    {
        try {
            $projectType = ProjectTypeService::getProjectType();
            if ($projectType) {
                return $projectType;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getProjectTypeStatus()
    {
        try {
            $status = ProjectTypeService::getProjectTypeStatus();
            if ($status) {
                return $status;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function storeUpdateProjectType($request, $id, $moduleMode)
    {
        try {
            if (ProjectTypeService::storeUpdateProjectType($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function findProjectType($id)
    {
        try {
            $projectType = ProjectTypeService::findProjectType($id);
            if ($projectType) {
                return $projectType;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteProjectType($projectType)
    {
        try {
            if (ProjectTypeService::deleteProjectType($projectType)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
