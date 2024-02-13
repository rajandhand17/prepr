<?php

namespace App\Http\Controllers\Api\Public\Project;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Project\ProjectResource;
use App\Repositories\Api\Public\Project\ProjectRepository;
use Exception;

class ProjectController extends AppBaseController
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function show($slug)
    {
        try {
            $fetchProject = $this->projectRepository->getProjectBasedOnSlug($slug);
            if ($fetchProject) {
                return $this->sendResponse(ProjectResource::make($fetchProject), __('responses.found_project_detail'), 200);
            }

            return $this->sendError(__('responses.found_not_project_detail'), 404);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            dd($slug, $action);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
