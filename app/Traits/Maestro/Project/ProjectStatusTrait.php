<?php

namespace App\Traits\Maestro\Project;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\ProjectStatusService;
use Exception;

trait ProjectStatusTrait
{
    private function getProjectStatus()
    {
        try {
            $projectStatus = ProjectStatusService::getProjectStatus();
            if ($projectStatus) {
                return $projectStatus;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function storeUpdateProjectStatus($request, $id, $moduleMode)
    {
        try {
            if (ProjectStatusService::storeUpdateProjectStatus($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function findProjectStatus($id)
    {
        try {
            $projectStatus = ProjectStatusService::findProjectStatus($id);
            if ($projectStatus) {
                return $projectStatus;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function deleteProjectStatus($projectStatus)
    {
        try {
            if (ProjectStatusService::deleteProjectStatus($projectStatus)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
