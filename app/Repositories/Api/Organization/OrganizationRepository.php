<?php

namespace App\Repositories\Api\Organization;

use App\Models\Organization;
use App\Services\OrganizationAddressService;
use App\Services\OrganizationMemberService;
use App\Services\OrganizationService;

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

    public function checkOrganizationExist($request)
    {
        $checkOrganization = $this->organizationService->checkOrganizationExist($request);
        if ($checkOrganization) {
            return true;
        }

        return false;
    }

    public function checkOrganizationExistInTrash($request)
    {
        $checkOrganization = $this->organizationService->checkOrganizationExistInTrash($request);
        if ($checkOrganization) {
            return true;
        }

        return false;
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
        } else {
            return false;
        }
    }

    public function organizationAddMembers($request, $organization_id)
    {
        $organization_member = $this->organizationMemberService->organizationAddMembers($request, $organization_id);
        if ($organization_member) {
            return $organization_member;
        } else {
            return false;
        }
    }

    public function viewOrganization($request, $slug)
    {
        try {
            return $this->organizationService->view($slug, $request->language);
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
            } else {
                return false;
            }
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

    public function getOrganizationList($language)
    {
        try {
            $organization = $this->organizationService->list($language);
            if ($organization) {
                return $organization;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteOrganization($slug, $language)
    {
        try {
            $organization = $this->organizationService->delete($slug, $language);
            if ($organization) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
