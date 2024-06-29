<?php

namespace App\Http\Controllers\Api\Public\ProjectInvitationManagement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Public\ProjectInvitationManagement\ProjectInvitationManagementRepository;
use Exception;
use Illuminate\Http\Request;

class ProjectInvitationManagementController extends AppBaseController
{
    private $projectInvitationManagementRepository;

    public function __construct(ProjectInvitationManagementRepository $projectInvitationManagementRepository)
    {
        $this->projectInvitationManagementRepository = $projectInvitationManagementRepository;
    }

    public function acceptOrRejectJoinRequest(Request $request, $slug, $action)
    {
        try {
            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendError(__('responses.project_not_found'), 403);
            }

            $checkLabStatus = $this->projectInvitationManagementRepository->checkJoinUnjoinStatus($request, $checkProjectExistsOrNot);
            if ($checkLabStatus) {
                $member_management = $this->projectInvitationManagementRepository->acceptOrRejectJoinRequest($request, $checkProjectExistsOrNot, $action);
                if ($member_management) {
                    return $this->sendResponse(null, __('responses.join_request_'.$action.'_successfully'));
                }

                return $this->sendError(__('responses.join_request_'.$action.'_failed'), 400);
            }

            return $this->sendError(__('responses.request_not_exist'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
