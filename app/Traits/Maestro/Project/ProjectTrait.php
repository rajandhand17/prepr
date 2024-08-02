<?php

namespace App\Traits\Maestro\Project;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\CategoryService;
use App\Services\Maestro\ChallengeService;
use App\Services\Maestro\LabService;
use App\Services\Maestro\ProjectIndustryService;
use App\Services\Maestro\ProjectService;
use App\Services\Maestro\ProjectStageService;
use App\Services\Maestro\ProjectStatusService;
use App\Services\Maestro\ProjectTypeService;
use App\Services\Maestro\ProjectVerticalService;
use App\Services\Maestro\UserService;
use Exception;

trait ProjectTrait
{
    private function getProjectsList()
    {
        try {
            $projects = ProjectService::getProjectsList();
            if ($projects) {
                return $projects;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function createProject($request)
    {
        try {
            if (ProjectService::createProject($request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getProjectById($id)
    {
        try {
            return ProjectService::getProjectById($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    private function getProjectAssociateItems($action, $project)
    {
        try {
            if ($action == 'edit') {
                $responseData['user'] = UserService::getUser($action, $project->user_id);
                $responseData['project_challenge'] = ChallengeService::getChallenge($action, $project->challenge_id);
                $responseData['project_lab'] = LabService::getLab($action, $project->lab_id);
            } else {
                $responseData['user'] = UserService::getUser($action, null);
                $responseData['project_challenge'] = ChallengeService::getChallenge($action, null);
                $responseData['project_lab'] = LabService::getLab($action, null);
            }
            $responseData['project_stage'] = ProjectStageService::getProjectStages();
            $responseData['project_category'] = CategoryService::getCategoryByType('project');
            $responseData['project_status'] = ProjectStatusService::getStatus();
            $responseData['project_type'] = ProjectTypeService::getTypes();
            $responseData['project_industry'] = ProjectIndustryService::getIndustries();
            $responseData['project_verticals'] = ProjectVerticalService::getVerticals();
            $responseData['project_privacy'] = ['0' => 'Public', '1' => 'Private'];
            $responseData['selected_member'] = [];

            return $responseData;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
