<?php

namespace App\Http\Controllers\Api\GO1;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\GO1\CreateResourceModuleRequest;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleResource;
use App\Repositories\Api\GO1\GO1Interface;
use Exception;

class GO1Controller extends AppBaseController
{
    public function __construct(public GO1Interface $go1Repository)
    {
    }

    public function index()
    {
        try {
            $data = $this->go1Repository->getCourseLists();
            if (!$data) {
                return $this->sendError(__('responses.go1_courses_fetched_failed'), 400);
            }

            return $this->sendResponse($data, __('responses.go1_courses_fetched_successfully'));
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateResourceModuleRequest $request)
    {
        try {
            $resource = $this->go1Repository->createResourceModule($request);
            if (!$resource) {
                return $this->sendResponse(__('responses.go1_resource_creation_failed'), 400);
            }

            return $this->sendResponse(ResourceModuleResource::make($resource), __('responses.go1_resource_creation_successful'));
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function listFilters($type)
    {
        try {
            $availableTypes = ['topics', 'providers'];

            if (!in_array($type, $availableTypes, true)) {
                return $this->sendError(__('responses.invalid_type'), 404);
            }

            $data = $this->go1Repository->listFilters($type);

            return $this->sendResponse($data[$type], __('responses.'.$type.'_list_successfully'));
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function playCourse($slug)
    {
        try {
            if (!$this->go1Repository->canPlayGO1Resoruces()) {
                return $this->sendError(__('responses.go1_play_content_denied'), 400);
            }

            $resourceModule = $this->go1Repository->getResourceModuleBySlug($slug);
            if (!$resourceModule) {
                return $this->sendError(__('responses.resource_module_slug_not_found'), 404);
            }

            if (!$resourceModule->is_go1) {
                return $this->sendError(__('responses.not_a_go1_resource'), 400);
            }

            $authenticatedLink = $this->go1Repository->playCourse($resourceModule->go1_course_id);
            if (!$authenticatedLink) {
                return $this->sendError(__('responses.play_course_failed'), 400);
            }

            return $this->sendResponse($authenticatedLink, __('responses.authenticated_link_fetched_successfully'));
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function webhook()
    {
        try {
            $webhook = $this->go1Repository->webhook(request()->all());
            if (!$webhook) {
                return $this->sendError(__('responses.webhook_failed'), 400);
            }

            return $this->sendResponse(null, __('responses.webhook_success'));
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
