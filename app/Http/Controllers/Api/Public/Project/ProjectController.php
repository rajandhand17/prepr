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
            $fetchProject = $this->projectRepository->getProjectBasedOnSlug($slug);
            if ($fetchProject) {
                $getColumnNameValue = $this->projectRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }

                $checkActivity = $this->projectRepository->checkSocialActivity($fetchProject->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_' . $action . '_project'), 400);
                }

                $captureActivity = $this->projectRepository->captureSocialActivity($fetchProject->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($captureActivity) {
                    return $this->sendResponse([], __('responses.' . $action . '_project_successfully'));
                }

            }
            return $this->sendError(__('responses.found_not_project_detail'), 404);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
