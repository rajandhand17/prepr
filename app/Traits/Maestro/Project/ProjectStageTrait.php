<?php

namespace App\Traits\Maestro\Project;

use App\Services\Maestro\ProjectStageService;
use Exception;

trait ProjectStageTrait
{
    private function getProjectStage()
    {
        try {
            $projectStage = ProjectStageService::getProjectStage();
            if ($projectStage) {
                return $projectStage;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function storeUpdateProjectStage($request, $id, $moduleMode)
    {
        try {
            if (ProjectStageService::storeUpdateProjectStage($request, $id, $moduleMode)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function findProjectStage($id)
    {
        try {
            $projectStage = ProjectStageService::findProjectStage($id);
            if ($projectStage) {
                return $projectStage;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteProjectStage($projectStage)
    {
        try {
            if (ProjectStageService::deleteProjectStage($projectStage)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
