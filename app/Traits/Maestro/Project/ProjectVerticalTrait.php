<?php

namespace App\Traits\Maestro\Project;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\ProjectVerticalService;
use Exception;

trait ProjectVerticalTrait
{
    private function getProjectVertical()
    {
        try {
            $projectVertical = ProjectVerticalService::getProjectVertical();
            if ($projectVertical) {
                return $projectVertical;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function storeUpdateProjectVertical($request, $id, $moduleMode)
    {
        try {
            if (ProjectVerticalService::storeUpdateProjectVertical($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function findProjectVertical($id)
    {
        try {
            $projectVertical = ProjectVerticalService::findProjectVertical($id);
            if ($projectVertical) {
                return $projectVertical;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function deleteProjectVertical($projectVertical)
    {
        try {
            if (ProjectVerticalService::deleteProjectVertical($projectVertical)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
