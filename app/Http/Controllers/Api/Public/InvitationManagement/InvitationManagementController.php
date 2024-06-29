<?php

/**
 * @OA\Tag(
 *     name="InvitationManagementController",
 *     description="Operations related to InvitationManagementController"
 * )
 */

namespace App\Http\Controllers\Api\Public\InvitationManagement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Public\InvitationManagement\InvitationManagementRepository;
use Illuminate\Http\Request;

class InvitationManagementController extends AppBaseController
{
    private $invitationManagementRepository;

    public function __construct(InvitationManagementRepository $invitationManagementRepository)
    {
        $this->invitationManagementRepository = $invitationManagementRepository;
    }

    public function acceptOrRejectComponentJoinRequest(Request $request, $component, $slug, $action)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component).' '.__('responses.not_found_required'), 403);
            }
            $checkComponentStatus = $this->invitationManagementRepository->checkComponentJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component);
            if ($checkComponentStatus) {
                $member_management = $this->invitationManagementRepository->acceptOrRejectComponentJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
                if ($member_management) {
                    return $this->sendResponse(null, __('responses.join_request_'.$action.'_successfully'));
                }

                return $this->sendError(__('responses.join_request_'.$action.'_failed'), 400);
            }

            return $this->sendError(__('responses.request_not_exist'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
