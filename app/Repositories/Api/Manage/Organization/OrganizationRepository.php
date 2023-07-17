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
            $organization = $this->organizationService->getOrganizationList($request);
            if ($organization) {
                return $organization;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function getOrganizationExistBasedOnSlug($slug)
    {
        try{
            return $this->organizationService->getOrganizationExistBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkOrganizationExistBasedOnTitle($request)
    {
        try{
            return $this->organizationService->checkOrganizationExistBasedOnTitle($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkOrganizationExistInTrashBasedOnTitle($request)
    {
        try{
            return $this->organizationService->checkOrganizationExistInTrashBasedOnTitle($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function uploadOrganizationProfileImage($request)
    {
        $upload_profile_image = $this->organizationService->uploadOrganizationProfileImage($request);
        if ($upload_profile_image) {
            return $upload_profile_image;
        } else {
            return false;
        }
    }

    public function uploadOrganizationCoverImage($request)
    {
        $upload_profile_image = $this->organizationService->uploadOrganizationCoverImage($request);
        if ($upload_profile_image) {
            return $upload_profile_image;
        } else {
            return false;
        }
    }

    public function createOrganization($request, $profile_image_path, $cover_image_path)
    {
        $organization = $this->organizationService->createOrganization($request, $profile_image_path, $cover_image_path);
        if ($organization) {
            return $organization;
        } else {
            return false;
        }
    }

    public function organizationAddAddress($request, $organization_id)
    {
        $organization = $this->organizationAddressService->createOrganizationAddress($request, $organization_id);
        if ($organization) {
            return $organization;
        }

        return false;
    }

    public function organizationAddMembers($request, $organization_id)
    {
        $organization_member = $this->organizationMemberService->organizationAddMembers($request, $organization_id);
        if ($organization_member) {
            return $organization_member;
        }

        return false;
    }

    public function getOrganization($request, $slug)
    {
        try {
            $organization = $this->organizationService->getOrganization($slug, $request->language);
            if ($organization) {
                return $organization;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkSlug($slug): bool
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

    public function updateOrganizationProfileImage($request)
    {
        try {
            $profile_image_path = $this->organizationService->updateOrganizationProfileImage($request);
            if ($profile_image_path) {
                return $profile_image_path;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateOrganizationCoverImage($request)
    {
        try {
            $cover_images_path = $this->organizationService->updateOrganizationCoverImage($request);
            if ($cover_images_path) {
                return $cover_images_path;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateOrganization($request, $cover_images_path, $profile_images_path, $slug)
    {
        try {
            $organization = $this->organizationService->updateOrganization($request, $cover_images_path, $profile_images_path, $slug);
            if ($organization) {
                return $organization;
            }

            return false;
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

}
