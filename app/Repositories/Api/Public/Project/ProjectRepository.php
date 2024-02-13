<?php

namespace App\Repositories\Api\Public\Project;

use App\Services\Public\ProjectService;
use App\Services\Public\ProjectSocialActivitiesService;
use Exception;

class ProjectRepository implements ProjectInterface
{
    private $projectService;
    private $projectSocialActivitiesService;

    public function __construct(ProjectService $projectService, ProjectSocialActivitiesService $projectSocialActivitiesService)
    {
        $this->projectService = $projectService;
        $this->projectSocialActivitiesService = $projectSocialActivitiesService;
    }

    public function getProjectBasedOnSlug($slug)
    {
        try {
            return $this->projectService->getProjectBasedOnSlug($slug);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->projectSocialActivitiesService->getColumnNameValue($action);
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkSocialActivity($projectId, $column, $action)
    {
        try {
            return $this->projectSocialActivitiesService->checkSocialActivity($projectId, $column, $action);
        } catch (Exception $e) {
            return false;
        }
    }

    public function captureSocialActivity($projectId, $column, $action)
    {
        try {
            return $this->projectSocialActivitiesService->captureSocialActivity($projectId, $column, $action);
        } catch (Exception $e) {
            return false;
        }
    }
}
