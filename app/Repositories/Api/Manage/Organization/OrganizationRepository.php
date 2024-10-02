<?php

namespace App\Repositories\Api\Manage\Organization;

use App\Helpers\UtilityHelper;
use App\Models\Organization;
use App\Services\Manage\OrganizationAddressService;
use App\Services\Manage\OrganizationCustomizationService;
use App\Services\Manage\OrganizationExternalLinkService;
use App\Services\Manage\OrganizationMemberService;
use App\Services\Manage\OrganizationService;

class OrganizationRepository implements OrganizationInterface
{
    private $organization;
    private $organizationAddressService;
    private $organizationMemberService;
    private $organizationService;
    private $organizationExternalLinkService;
    private $organizationCustomizationService;

    public function __construct(Organization $organization, OrganizationService $organizationService, OrganizationAddressService $organizationAddressService, OrganizationMemberService $organizationMemberService, OrganizationExternalLinkService $organizationExternalLinkService, OrganizationAddressService $organizationAddressService2, OrganizationCustomizationService $organizationCustomizationService)
    {
        $this->organization = $organization;
        $this->organizationAddressService = $organizationAddressService;
        $this->organizationMemberService = $organizationMemberService;
        $this->organizationService = $organizationService;
        $this->organizationExternalLinkService = $organizationExternalLinkService;
        $this->organizationCustomizationService = $organizationCustomizationService;
    }

    public function getOrganizationList($request)
    {
        try {
            return $this->organizationService->getOrganizationList($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getOrganizationBasedOnSlug($slug)
    {
        try {
            return $this->organizationService->getOrganizationBasedOnSlug($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkOrganizationExistBasedOnTitle($request)
    {
        try {
            return $this->organizationService->checkOrganizationExistBasedOnTitle($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkOrganizationExistInTrashBasedOnTitle($request)
    {
        try {
            return $this->organizationService->checkOrganizationExistInTrashBasedOnTitle($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function uploadOrganizationProfileImage($request)
    {
        try {
            return $this->organizationService->uploadOrganizationProfileImage($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function uploadOrganizationCoverImage($request)
    {
        try {
            return $this->organizationService->uploadOrganizationCoverImage($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createOrganization($request, $profile_image_path, $cover_image_path)
    {
        try {
            return $this->organizationService->createOrganization($request, $profile_image_path, $cover_image_path);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createOrganizationAddress($request, $organization_id)
    {
        try {
            return $this->organizationAddressService->createOrganizationAddress($request, $organization_id);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createOrganizationMembers($request, $organization_id)
    {
        try {
            return $this->organizationMemberService->createOrganizationMembers($request, $organization_id);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createOrganizationExternalLinks($request, $organizationId)
    {
        try {
            return $this->organizationExternalLinkService->createOrganizationExternalLinks($request, $organizationId);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateOrganization($request, $cover_images_path, $profile_images_path, $slug)
    {
        try {
            return $this->organizationService->updateOrganization($request, $cover_images_path, $profile_images_path, $slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updatesOrganizationAddress($request, $organization_id)
    {
        try {
            return $this->organizationAddressService->updatesOrganizationAddress($request, $organization_id);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updatesOrganizationMembers($request, $organization_id)
    {
        try {
            return $this->organizationMemberService->updatesOrganizationMembers($request, $organization_id);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateOrganizationExternalLinks($request, $organizationId)
    {
        try {
            return $this->organizationExternalLinkService->updateOrganizationExternalLinks($request, $organizationId);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function deleteOrganization($organizationData, $request)
    {
        try {
            return  $this->organizationService->deleteOrganization($organizationData, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            $organization = $this->organizationService->checkSlug($slug);
            if ($organization) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $organizationSlug = $this->organizationService->checkNameExistsOrNot($title);

            return $organizationSlug;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getOrganizationListOnlyNameAndUuid($request)
    {
        try {
            return $this->organizationService->getOrganizationListOnlyNameAndUuid($request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function selectPlan($organization, $request)
    {
        try {
            return $this->organizationService->selectPlan($organization, $request);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function planData($organizationData)
    {
        try {
            return $this->organizationService->planData($organizationData);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateOrganizationCustomLoginRegistration($request, $organizationData)
    {
        try {
            return $this->organizationCustomizationService->updateOrganizationCustomLoginRegistration($request, $organizationData);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function incrementView(Organization $organization)
    {
        try {
            return $this->organizationService->incrementView($organization);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkOrganizationCustomizationData($slug)
    {
        try {
            return $this->organizationCustomizationService->checkOrganizationCustomizationData($slug);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
