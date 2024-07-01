<?php

namespace App\Http\Controllers\Api\Manage\Unified;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Unified\ListEmployeeRequest;
use App\Http\Requests\Manage\Unified\UnifiedIntegrationRequest;
use App\Http\Requests\Manage\Unified\UnifiedUserInviteRequest;
use App\Repositories\Api\Manage\UnifiedConnection\UnifiedConnectionRepository;
use Symfony\Component\HttpFoundation\Response;

class UnifiedController extends AppBaseController
{
    /**
     * @param UnifiedConnectionRepository $unifiedConnectionRepository
     */
    public function __construct(protected UnifiedConnectionRepository $unifiedConnectionRepository)
    {
    }

    public function integrations(UnifiedIntegrationRequest $request)
    {
        try {
            $integrations = $this->unifiedConnectionRepository->getIntegrations($request->validated(), auth()->user());

            if ($integrations !== false) {
                return $this->sendResponse($integrations, __('responses.unified_integrations_list_success'));
            }

            return $this->sendError(__('responses.unified_integrations_list_failed'), Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function listEmployee(ListEmployeeRequest $request)
    {
        try {
            $body = $request->formatted();
            $employeeList = $this->unifiedConnectionRepository->listEmployee($request->get('connection_id'), data_get($body, 'state'));
            if (!$employeeList) {
                return $this->sendError(__('responses.unified_fetch_employee_list_failed'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendResponse($employeeList, __('responses.unified_fetch_employee_list_success'));
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function inviteMembers(UnifiedUserInviteRequest $request)
    {
        try {
            $invitation = $this->unifiedConnectionRepository->inviteMembers($request->formatted());
            if ($invitation === false) {
                return $this->sendError(__('responses.unified_invite_members_failed'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendResponse($invitation, __('responses.unified_invite_member_success'));
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
