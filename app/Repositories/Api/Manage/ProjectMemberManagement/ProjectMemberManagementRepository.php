<?php

namespace App\Repositories\Api\Manage\ProjectMemberManagement;

use App\Services\Manage\ProjectMemberManagementService;
use Exception;

class ProjectMemberManagementRepository implements ProjectMemberManagementInterface
{
    private $projectMemberManagementService;

    public function __construct(ProjectMemberManagementService $projectMemberManagementService)
    {
        $this->projectMemberManagementService = $projectMemberManagementService;
    }

    public function addMembers($projectData, $request)
    {
        try {
            $participatesList = [];
            if ($request->invite_type == 'csv') {
                $participatesList = $this->projectMemberManagementService->fetchDataFromCSV($request);
                if ($participatesList) {
                    if (!$participatesList && !count($participatesList) > 0) {
                        return false;
                    }
                }
            }

            if (is_array($participatesList) && count($participatesList) > 0) {
                $addParticipates = $this->projectMemberManagementService->addParticipates($projectData, $request, $participatesList);
            }

            dd($participatesList);
        } catch (Exception $e) {
            return false;
        }
    }
}
