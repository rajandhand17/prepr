<?php

namespace App\Traits\Maestro\Project;

use App\Services\Maestro\Project\ProjectService;
use Exception;

trait ProjectTrait
{
    private function createProject($request)
    {
        try {
            if (ProjectService::createProject($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function deleteProjectById($id)
    {
        try {
            if (ProjectService::deleteProject($id)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getProjectById($id)
    {
        try {
            return ProjectService::getProjectById($id);
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateProjectById($id, $request)
    {
        try {
            if (ProjectService::updateProjectById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getProjectAssociateItems($type)
    {
        try {
            $associateItems = ProjectService::getProjectAssociateItems($type);
            if ($associateItems) {
                return $associateItems;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
