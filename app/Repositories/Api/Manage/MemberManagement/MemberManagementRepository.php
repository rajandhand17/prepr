<?php

namespace App\Repositories\Api\Manage\MemberManagement;

use App\Helpers\UtilityHelper;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\RolesService;
use Response;

class MemberManagementRepository implements MemberManagementInterface
{
    private $memberManagementService;
    private $roleService;

    public function __construct(MemberManagementService $memberManagementService, RolesService $roleService)
    {
        $this->memberManagementService = $memberManagementService;
        $this->roleService = $roleService;
    }

    public function getMembers($componentCollectionObject, $component, $request)
    {
        try {
            return $this->memberManagementService->getComponentBasedUsers($componentCollectionObject, $component, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getTemplate($request, $component)
    {
        try {
            return $this->memberManagementService->getTemplate($request, $component);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function downloadSample()
    {
        try {
            $headers = [
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename=member-management-sample.csv',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];
            $columns = ['Name', 'Email'];
            $callback = function () use ($columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getRoles($role_type)
    {
        try {
            return $this->roleService->getRoles($role_type);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function addMembers($componentCollectionObject, $component, $request)
    {
        try {
            $memberList = [];
            if ($request->invite_type == 'csv') {
                $memberList = $this->memberManagementService->getRecordsFromCsv($request);
                if ($memberList) {
                    if (!$memberList && !count($memberList) > 0) {
                        return false;
                    }
                }
            }
            if ($request->invite_type == 'email') {
                $memberList = $this->memberManagementService->getRecordsFromEmailArray($request);
                if ($memberList) {
                    if (!$memberList && !count($memberList) > 0) {
                        return false;
                    }
                }
            }
            if (is_array($memberList) && count($memberList) > 0) {
                $checkStatus = $this->memberManagementService->addMembers($componentCollectionObject, $component, $request, $memberList);
                if ($checkStatus) {
                    return $checkStatus;
                }

                return false;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteMembers($checkComponentBasedOnSlug, $component, $request)
    {
        try {
            return $this->memberManagementService->deleteMembers($checkComponentBasedOnSlug, $component, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component)
    {
        try {
            return $this->memberManagementService->checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action)
    {
        try {
            return $this->memberManagementService->acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function changeRole($request, $component)
    {
        try {
            return $this->memberManagementService->changeRoleByUuid($request, $component);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
