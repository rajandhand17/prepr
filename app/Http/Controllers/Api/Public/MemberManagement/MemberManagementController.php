<?php

/**
 * @OA\Tag(
 *     name="MemberManagementController",
 *     description="Operations related to MemberManagementController"
 * )
 */

namespace App\Http\Controllers\Api\Public\MemberManagement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Public\MemberManagement\MemberManagementRepository;
use Illuminate\Http\Request;

class MemberManagementController extends AppBaseController
{
    private $memberManagementRepository;

    public function __construct(MemberManagementRepository $memberManagementRepository)
    {
        $this->memberManagementRepository = $memberManagementRepository;
    }

    public function acceptOrRejectLabJoinRequest(Request $request, $component, $slug, $action)
    {
        try {
            $checkComponentBasedOnSlug = UtilityHelper::checkComponentSlugExistOrNot($component, $slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(ucfirst($component) . ' ' . __('responses.not_found_required'), 403);
            }
            $checkLabStatus = $this->memberManagementRepository->checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component);
            if ($checkLabStatus) {
                $member_management = $this->memberManagementRepository->acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
                if ($member_management) {
                    return $this->sendResponse(null, __('responses.join_request_' . $action . '_successfully'));
                }

                return $this->sendError(__('responses.join_request_' . $action . '_failed'), 400);
            }

            return $this->sendError(__('responses.request_not_exist'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
