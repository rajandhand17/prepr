<?php

namespace App\Repositories\Api\Public\InvitationManagement;

use App\Services\Public\InvitationManagementService;
use App\Services\Public\RolesService;

class InvitationManagementRepository implements InvitationManagementInterface
{
    private $invitationManagementService;
    private $roleService;

    public function __construct(InvitationManagementService $invitationManagementService, RolesService $roleService)
    {
        $this->invitationManagementService = $invitationManagementService;
        $this->roleService = $roleService;
    }

    public function checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component)
    {
        try {
            return  $this->invitationManagementService->checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action)
    {
        try {
            return $this->invitationManagementService->acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
        } catch (\Exception $e) {
            return false;
        }
    }
}
