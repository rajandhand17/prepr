<?php

namespace App\Repositories\Api\Public\InvitationManagement;

use App\Services\Public\MemberManagementService;
use App\Services\Public\RolesService;

class InvitationManagementRepository implements InvitationManagementInterface
{
    private $memberManagementService;
    private $roleService;

    public function __construct(MemberManagementService $memberManagementService, RolesService $roleService)
    {
        $this->memberManagementService = $memberManagementService;
        $this->roleService = $roleService;
    }

    public function checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component)
    {
        try {
            return  $this->memberManagementService->checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action)
    {
        try {
            return $this->memberManagementService->acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
        } catch (\Exception $e) {
            return false;
        }
    }
}
