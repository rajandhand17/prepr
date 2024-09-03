<?php

namespace App\Http\Controllers\Api\Manage\CampusConnect;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\CampusConnect\CampusConnectRepository;
use Exception;

class CampusConnectController extends AppBaseController
{
    public function __construct(private CampusConnectRepository $campusConnectRepository)
    {
    }

    public function listSchools()
    {
        try {
            $schools = $this->campusConnectRepository->listSchools();

            if (!$schools) {
                return $this->sendError(__('responses.school_fetched_failed'));
            }

            return $this->sendResponse($schools, __('responses.school_fetched_success'));
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
