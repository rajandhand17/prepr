<?php

namespace App\Http\Controllers\Api\Manage\Airmeet;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Airmeet\VerifyAirmeetEventRequest;
use App\Repositories\Api\Manage\AirmeetEvent\AirmeetEventRepository;
use Illuminate\Http\JsonResponse;

class AirmeetEventController extends AppBaseController
{
    public function __construct(protected AirmeetEventRepository $airmeetEventRepository)
    {
    }

    /**
     * @param VerifyAirmeetEventRequest $request
     *
     * @return JsonResponse
     */
    public function verifyEvent(VerifyAirmeetEventRequest $request): JsonResponse
    {
        try {
            /**
             * FETCHING EVENT DETAILS.
             */
            $airmeetEventDetails = $this->airmeetEventRepository->getVerifiedEventDetails($request->validated('event_id'));

            /**
             * INVALID EVENT.
             */
            if ($airmeetEventDetails === false) {
                return $this->sendError(__('Invalid event !'));
            }

            /**
             * FORMATTED EVENT DETAILS.
             */
            $eventDetails = [
                'name'       => data_get($airmeetEventDetails, 'name'),
                'thumbnail'  => data_get($airmeetEventDetails, 'master_img_url'),
                'start_time' => data_get($airmeetEventDetails, 'start_time'),
                'end_time'   => data_get($airmeetEventDetails, 'end_time'),
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.invalid_event'));
        }

        return $this->sendResponse($eventDetails, 'responses.even_details');
    }
}
