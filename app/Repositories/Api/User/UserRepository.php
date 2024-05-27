<?php

namespace App\Repositories\Api\User;

use App\Services\Public\MemberManagementService;
use App\Services\Public\OrganizationService;
use App\Services\UserService;

class UserRepository implements UserInterface
{
    protected $userService;
    protected $organizationService;
    protected $memberManagementService;

    public function __construct(UserService $userService, OrganizationService $organizationService, MemberManagementService $memberManagementService)
    {
        $this->userService = $userService;
        $this->organizationService = $organizationService;
        $this->memberManagementService = $memberManagementService;
    }

    public function getUsers($request)
    {
        try {
            return  $this->userService->getUsers($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function organizationListing()
    {
        try {
            $user = auth()->user();
            $userId = $user->id;
            $userEmail = $user->email;
            $fetchOrganizationIds = collect();
            if ($user->hasRole(['organization_owner', 'organization_manager'])) {
                $fetchOrganizationIds = $this->organizationService->fetchOrganizationIds($userId);
            }

            if ($user->hasRole(['lab_manager', 'challenge_manager', 'resource_manager', 'user'] && $fetchOrganizationIds->isEmpty())) {
                $fetchOrganizationIds = $this->memberManagementService->fetchComponentBasedOrganizationIds($userEmail);
            }

            return $this->organizationService->fetchOrganizations($fetchOrganizationIds);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function setOrganizationPreference($organizationId)
    {
        try {
            return $this->userService->setOrganizationPreference($organizationId);
        } catch (\Exception $e) {
            return false;
        }
    }
}
