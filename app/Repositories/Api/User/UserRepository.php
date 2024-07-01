<?php

namespace App\Repositories\Api\User;

use App\Helpers\UtilityHelper;
use App\Services\Manage\OrganizationService as ManageOrganizationService;
use App\Services\Manage\OrganizationTypeModeService;
use App\Services\Public\MemberManagementService;
use App\Services\Public\OrganizationService;
use App\Services\UserService;

class UserRepository implements UserInterface
{
    protected $userService;
    protected $organizationService;
    protected $memberManagementService;
    protected $manageOrganizationService;
    protected $organizationTypeModeService;

    public function __construct(UserService $userService, OrganizationService $organizationService, MemberManagementService $memberManagementService, ManageOrganizationService $manageOrganizationService, OrganizationTypeModeService $organizationTypeModeService)
    {
        $this->userService = $userService;
        $this->organizationService = $organizationService;
        $this->memberManagementService = $memberManagementService;
        $this->manageOrganizationService = $manageOrganizationService;
        $this->organizationTypeModeService = $organizationTypeModeService;
    }

    public function getUsers($request)
    {
        try {
            return  $this->userService->getUsers($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function organizationListing($request)
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

            $organizationData = $this->organizationService->fetchOrganizations($request, $fetchOrganizationIds);
            // Marking the organization as default if the count of retrived organization is only 1.
            if ($organizationData->count() > (int) '0') {
                if ($user->preferred_organization == null) {
                    $this->setOrganizationPreference($organizationData[0]->id);
                } else {
                    $fetchOrganization = ManageOrganizationService::getOrganizationExistBasedOnId($user->preferred_organization);
                    if (empty($fetchOrganization)) {
                        $this->setOrganizationPreference($organizationData[0]->id);
                    }
                }
            }

            return $organizationData;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function setOrganizationPreference($organizationId)
    {
        try {
            return $this->userService->setOrganizationPreference($organizationId);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function userOnboarding()
    {
        try {
            return $this->userService->userOnboarding();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function organizationOnboarding($organizationId, $request)
    {
        try {
            return $this->manageOrganizationService->organizationOnboarding($organizationId, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function storeOrganizationType($organizationId, $request)
    {
        try {
            return $this->organizationTypeModeService->storeOrganizationType($organizationId, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
