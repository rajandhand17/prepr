<?php

namespace App\Repositories\Api\Public\ProjectInvitationManagement;

use App\Helpers\UtilityHelper;
use App\Services\Public\ProjectMemberManagementService;
use Exception;

class ProjectInvitationManagementRepository implements ProjectInvitationManagementInterface
{
    private $projectMemberManagementService;

    public function __construct(ProjectMemberManagementService $projectMemberManagementService)
    {
        $this->projectMemberManagementService = $projectMemberManagementService;
    }

    public function checkJoinUnjoinStatus($request, $projectData)
    {
        try {
            return  $this->projectMemberManagementService->checkJoinUnjoinStatus($request, $projectData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function acceptOrRejectJoinRequest($request, $projectData, $action)
    {
        try {
            return $this->projectMemberManagementService->acceptOrRejectJoinRequest($request, $projectData, $action);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
