<?php

namespace App\Repositories\Api\Manage\Organization;

use App\Models\Organization;
use App\Services\Manage\OrganizationAddressService;
use App\Services\Manage\OrganizationMemberService;
use App\Services\Manage\OrganizationService;

class OrganizationRepository implements OrganizationInterface
{
    private $organization;
    private $organizationAddressService;
    private $organizationMemberService;
    private $organizationService;

    public function __construct(Organization $organization, OrganizationService $organizationService, OrganizationAddressService $organizationAddressService, OrganizationMemberService $organizationMemberService, OrganizationAddressService $organizationAddressService2)
    {
        $this->organization = $organization;
        $this->organizationAddressService = $organizationAddressService;
        $this->organizationMemberService = $organizationMemberService;
        $this->organizationService = $organizationService;
    }

    public function getOrganizationList($request)
    {
        try {
            return $this->organizationService->getOrganizationList($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getOrganizationExistBasedOnSlug($slug)
    {
        try {
            return $this->organizationService->getOrganizationExistBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkOrganizationExistBasedOnTitle($request)
    {
        try {
            return $this->organizationService->checkOrganizationExistBasedOnTitle($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkOrganizationExistInTrashBasedOnTitle($request)
    {
        try {
            return $this->organizationService->checkOrganizationExistInTrashBasedOnTitle($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function uploadOrganizationProfileImage($request)
    {
        try {
            return $this->organizationService->uploadOrganizationProfileImage($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function uploadOrganizationCoverImage($request)
    {
        try {
            return $this->organizationService->uploadOrganizationCoverImage($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createOrganization($request, $profile_image_path, $cover_image_path)
    {
        try {
            return $this->organizationService->createOrganization($request, $profile_image_path, $cover_image_path);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createOrganizationAddress($request, $organization_id)
    {
        try {
            return $this->organizationAddressService->createOrganizationAddress($request, $organization_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createOrganizationMembers($request, $organization_id)
    {
        try {
            return $this->organizationMemberService->createOrganizationMembers($request, $organization_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateOrganizationProfileImage($request)
    {
        try {
            return $this->organizationService->updateOrganizationProfileImage($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateOrganizationCoverImage($request)
    {
        try {
            return $this->organizationService->updateOrganizationCoverImage($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateOrganization($request, $cover_images_path, $profile_images_path, $slug)
    {
        try {
            return $this->organizationService->updateOrganization($request, $cover_images_path, $profile_images_path, $slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updatesOrganizationAddress($request, $organization_id)
    {
        try {
            return $this->organizationAddressService->updatesOrganizationAddress($request, $organization_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updatesOrganizationMembers($request, $organization_id)
    {
        try {
            return $this->organizationMemberService->updatesOrganizationMembers($request, $organization_id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteOrganization($slug, $language)
    {
        try {
            return  $this->organizationService->deleteOrganization($slug, $language);
        } catch (\Exception $e) {
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
            return false;
        }
    }
}
