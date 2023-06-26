<?php

namespace App\Repositories\Api\MemberManagement;

use App\Services\MemberManagementService;
use App\Services\RolesService;
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

    public function getMembers($component, $slug, $request)
    {
        try {
            return $this->memberManagementService->index($component, $slug, $request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteMembers($component, $slug, $request)
    {
        try {
            return $this->memberManagementService->delete($component, $slug, $request);
        } catch (\Exception $e) {
            return false;
        }
    }

     public function addMembers($componentCollectionObject, $component, $request)
     {
         try {
             $memberList = [];
             if ($request->invite_type == 'csv') {
                 $memberList = $this->memberManagementService->getRecordsFromCsv($request);
                 if (!$memberList && !count($memberList) > 0) {
                     return false;
                 }
             }
             if ($request->invite_type == 'email') {
                 $memberList = $this->memberManagementService->getRecordsFromEmailArray($request);
                 if (!$memberList && !count($memberList) > 0) {
                     return false;
                 }
             }

             if (is_array($memberList) && count($memberList) > 0) {
                 $checkStatus = $this->memberManagementService->addMembers($componentCollectionObject, $component, $request, $memberList);

                 if ($checkStatus != false) {
                     return $checkStatus;
                 }

                 return false;
             }

             return false;
         } catch (\Exception $e) {
             return false;
         }
     }

    /**
     * @return MemberManagementService
     */
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
            return false;
        }
    }

    public function getRoles($role_type)
    {
        try {
            return $this->roleService->getRoles($role_type);
        } catch (\Exception $e) {
            return false;
        }
    }
}
