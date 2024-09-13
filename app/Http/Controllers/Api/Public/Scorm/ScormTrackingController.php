<?php

namespace App\Http\Controllers\Api\Public\Scorm;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Public\Scorm\ScormTrackingRequest;
use App\Repositories\Api\Public\Scorm\ScormTracking\ScormTrackingRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ScormTrackingController extends AppBaseController
{
    public function __construct(protected ScormTrackingRepository $scormTrackingRepository)
    {
    }

    /**
     * @param ScormTrackingRequest $request
     *
     * @return JsonResponse
     */
    public function trackProgress(ScormTrackingRequest $request): JsonResponse
    {
        try {
            $scormUser = $request->get('scormUser');
            $tracking = $this->scormTrackingRepository->store(
                $scormUser->id,
                $request->validated('sco_uuid'),
                $request->validated('version'),
                $request->validated('cmi', [])
            );

            if ($tracking) {
                return $this->sendResponse($tracking, __('responses.progress_tracking_success'));
            }

            return $this->sendError(__('responses.failed_to_track_progress'), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_track_progress'), Response::HTTP_BAD_REQUEST);
        }
    }
}
