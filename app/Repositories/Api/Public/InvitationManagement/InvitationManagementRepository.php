<?php

namespace App\Repositories\Api\Public\InvitationManagement;

use App\Helpers\UtilityHelper;
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

    public function checkComponentJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component)
    {
        try {
            return  $this->memberManagementService->checkComponentJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function acceptOrRejectComponentJoinRequest($request, $checkComponentBasedOnSlug, $component, $action)
    {
        try {
            return $this->memberManagementService->acceptOrRejectComponentJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
