<?php

namespace App\Http\Controllers\Api\Scorm;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Scorm\UploadScormRequest;
use App\Models\ResourceModule;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleRepository;
use App\Repositories\Api\Scorm\ScormRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResourceScormController extends AppBaseController
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
            return $this->sendError(__('responses.failed_to_upload_scorm_file'), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $slug
     *
     * @return JsonResponse
     */
    public function scormUrl(string $slug): JsonResponse
    {
        try {
            $resource = $this->resourceModuleRepository->getResourceModuleBasedOnSlug($slug);

            if (!$resource) {
                return $this->sendError(__('responses.resource_module_not_found'), Response::HTTP_NOT_FOUND);
            }

            $scorm = $resource->scorm;

            if (!$scorm) {
                return $this->sendError(__('responses.resource_doesn_t_have_a_scorm_file'), Response::HTTP_NOT_FOUND);
            }

            $scormPlayerUrl = $this->scormRepository->generateScormPlayerUrl($scorm);

            if (!$scormPlayerUrl) {
                return $this->sendError(__('responses.failed_to_get_scorm_url'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendResponse([
                'url' => $scormPlayerUrl,
            ], __('responses.scorm_player_link'));
        } catch (\Exception $exception) {
            return $this->sendError(__('responses.failed_to_get_scorm_url'), Response::HTTP_BAD_REQUEST);
        }
    }
}
