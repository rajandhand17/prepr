<?php

namespace App\Repositories\Api\Public\Project;

use App\Services\Public\ProjectService;
use Exception;

class ProjectRepository implements ProjectInterface
{
    private $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function getProjectBasedOnSlug($slug)
    {
        try {
            return $this->projectService->getProjectBasedOnSlug($slug);
        } catch (Exception $e) {
            return false;
        }
    }
}
