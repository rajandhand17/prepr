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
            $membersList = $this->projectMemberManagementRepository->addMembers($checkProjectExistsOrNot, $request);
            dd($checkProjectExistsOrNot);
        } catch (Exception $e) {
            dd($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
