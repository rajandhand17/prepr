<?php

namespace App\Http\Controllers\Api\Public\Scorm;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Scorm\ScormResource;
use App\Repositories\Api\Public\Scorm\ScormRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScormController extends AppBaseController
{
    public function __construct(
        protected ScormRepository $scormRepository,
    ) {
    }

    /**
     * @param string  $uuid
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function show(Request $request, string $uuid): JsonResponse
    {
        try {
            $scormUser = $request->get('scormUser') ?? null;
            $scormDetails = $this->scormRepository->getScorm($uuid, $scormUser);

            if ($scormDetails) {
                return $this->sendResponse(new ScormResource($scormDetails), __('responses.scorm_details'));
            }

            return $this->sendError(__('responses.failed_to_fetch_scorm_details'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_scorm_details'), Response::HTTP_BAD_REQUEST);
        }
    }
}
