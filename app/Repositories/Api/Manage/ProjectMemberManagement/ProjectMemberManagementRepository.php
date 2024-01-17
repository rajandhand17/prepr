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

    public function addParticipates($projectData, $request)
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
                if ($addParticipates) {
                    return $addParticipates;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
