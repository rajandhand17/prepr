<?php

namespace App\Http\Controllers\Api\Manage\ResourceModule;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ResourceModule\UploadScormRequest;
use App\Models\ResourceModule;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleRepository;
use App\Repositories\Api\Manage\Scorm\ScormRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResourceModuleScormController extends AppBaseController
{
    public function __construct(
        protected ResourceModuleRepository $resourceModuleRepository,
        protected ScormRepository $scormRepository
    ) {
    }

    /**
     * @tutorial Upload Scorm File
     *
     * @param string             $slug
     * @param UploadScormRequest $request
     *
     * @return JsonResponse
     */
    public function upload(string $slug, UploadScormRequest $request): JsonResponse
    {
        try {
            $resource = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);
            if ($resource->is_accessible === '0') {
                return $this->sendError(__('responses.resource_module_not_accessible'), 403);
            }
            if (!$resource) {
                return $this->sendError(__('responses.resource_module_not_found'), Response::HTTP_NOT_FOUND);
            }
            $scormDetails = $this->scormRepository->upload(
                ResourceModule::class,
                $resource->id,
                $request->file('scorm_file'),
                $resource->scorm
            );

            if ($scormDetails) {
                return $this->sendResponse(null, __('responses.scorm_file_has_been_uploaded'));
            }

            return $this->sendError(__('responses.failed_to_upload_scorm_file'), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_upload_scorm_file'), Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteScorm(string $slug)
    {
        try {
            $resource = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);

            if ($resource->is_accessible === '0') {
                return $this->sendError(__('responses.resource_module_not_accessible'), 403);
            }

            if (!$resource) {
                return $this->sendError(__('responses.resource_module_not_found'), Response::HTTP_NOT_FOUND);
            }

            if ($resource->scorm) {
                $deleteScorm = $this->scormRepository->delete($resource->scorm);
                if ($deleteScorm !== false) {
                    return $this->sendResponse([], __('responses.scorm_file_deleted'));
                }

                return $this->sendError(__('responses.failed_to_delete_scorm_file'), Response::HTTP_BAD_REQUEST);
            } else {
                return $this->sendError(__('responses.no_scorm_file_associated_with_the_resource'), Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_delete_scorm_file'), Response::HTTP_BAD_REQUEST);
        }
    }
}
