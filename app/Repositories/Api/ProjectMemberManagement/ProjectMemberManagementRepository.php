<?php

namespace App\Repositories\Api\ProjectMemberManagement;

use App\Helpers\UtilityHelper;
use App\Services\ProjectMemberManagementService;
use Exception;
use Response;

class ProjectMemberManagementRepository implements ProjectMemberManagementInterface
{
    private $projectMemberManagementService;

    public function __construct(ProjectMemberManagementService $projectMemberManagementService)
    {
        $this->projectMemberManagementService = $projectMemberManagementService;
    }

    public function getRoles()
    {
        try {
            return $this->projectMemberManagementService->getRoles();
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getProjectBasedParticipants($projectData, $request)
    {
        try {
            return $this->projectMemberManagementService->getProjectBasedParticipants($projectData, $request);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getTemplate($requestLang)
    {
        try {
            return $this->projectMemberManagementService->getTemplate($requestLang);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function downloadSample()
    {
        try {
            $headers = [
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename=project-member-management.csv',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];
            $columns = ['Name', 'Email', 'Access'];
            $callback = function () use ($columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                $dropdownOptions = ['viewer', 'editor'];
                foreach ($dropdownOptions as $option) {
                    fputcsv($file, ['', '', $option]);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
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

            if ($request->invite_type == 'email') {
                $participatesList = $this->projectMemberManagementService->fetchDataFromEmailArray($request);
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
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkProjectJoinUnjoinStatus($userEmail, $projectData)
    {
        try {
            return $this->projectMemberManagementService->checkProjectJoinUnjoinStatus($userEmail, $projectData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function acceptOrRejectProjectJoinRequest($request, $projectData, $action)
    {
        try {
            return $this->projectMemberManagementService->acceptOrRejectProjectJoinRequest($request, $projectData, $action);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkParticipantsUUID($projectId, $uuid)
    {
        try {
            return $this->projectMemberManagementService->checkParticipantsUUID($projectId, $uuid);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkCurrentProjectRole($projectId, $uuid, $role)
    {
        try {
            return $this->projectMemberManagementService->checkCurrentProjectRole($projectId, $uuid, $role);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function updateProjectRole($projectId, $uuid, $role)
    {
        try {
            return $this->projectMemberManagementService->updateProjectRole($projectId, $uuid, $role);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function deleteParticipates($projectData, $request)
    {
        try {
            return $this->projectMemberManagementService->deleteParticipates($projectData, $request);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkParticipantProjectJoinUnjoinStatus($userEmail, $projectData)
    {
        try {
            return $this->projectMemberManagementService->checkParticipantProjectJoinUnjoinStatus($userEmail, $projectData);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function participantAcceptOrRejectJoinRequest($userEmail, $projectData, $action)
    {
        try {
            return $this->projectMemberManagementService->participantAcceptOrRejectJoinRequest($userEmail, $projectData, $action);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
