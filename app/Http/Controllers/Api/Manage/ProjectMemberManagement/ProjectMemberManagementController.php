<?php

namespace App\Http\Controllers\Api\Manage\ProjectMemberManagement;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\ProjectMemberManagement\ProjectMemberManagementRepository;
use Exception;
use Illuminate\Http\Request;

class ProjectMemberManagementController extends AppBaseController
{
    private $projectMemberManagementRepository;

    public function __construct(ProjectMemberManagementRepository $projectMemberManagementRepository)
    {
        $this->projectMemberManagementRepository = $projectMemberManagementRepository;
    }

    public function create(Request $request)
    {
        try {
            $checkProjectExistsOrNot = UtilityHelper::checkComponentSlugExistOrNot('project', $request->slug);
            if ($checkProjectExistsOrNot == false) {
                return $this->sendResponse([], __('responses.project_not_found'), 403);
            }
            $participatesList = $this->projectMemberManagementRepository->addParticipates($checkProjectExistsOrNot, $request);
            if ((count($participatesList['invalid_emails']) > 0 || count($participatesList['already_members']) > 0) && count($participatesList['invited_emails']) < 1) {
                return $this->sendError($participatesList['add_participant_response'], 403);
            } elseif ($participatesList) {
                return $this->sendResponse($participatesList, $participatesList['add_participant_response']);
            }

            return $this->sendError(__('responses.create_member_manger_failed'), 403);
        } catch (Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
