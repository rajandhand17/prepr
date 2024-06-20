<?php

namespace App\Http\Controllers\Api\Public\ResourceModule;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Public\ResourceModule\ResourceModuleRepository;
use App\Repositories\Api\Public\Scorm\ScormRepository;
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
            if ($resource->is_accessible == '0') {
                return $this->sendError(__('responses.resource_module_not_accessible'), 403);
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
