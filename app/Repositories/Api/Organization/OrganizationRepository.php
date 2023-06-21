<?php

namespace App\Repositories\Api\Organization;

use App\Models\Organization;
use App\Services\OrganizationAddressService;
use App\Services\OrganizationMemberService;
use App\Services\OrganizationService;

class OrganizationRepository implements OrganizationInterface
{
    private $organization;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function checkOrganizationExist($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember)
    {
        $checkOrganization = $organizationService->checkOrganizationExist($request);
        if ($checkOrganization) {
            return true;
        }

        return false;
    }

    public function checkOrganizationExistInTrash($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember)
    {
        $checkOrganization = $organizationService->checkOrganizationExistInTrash($request);
        if ($checkOrganization) {
            return true;
        }

        return false;
    }

    public function uploadOrganizationProfileImage($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember)
    {
        $upload_profile_image = $organizationService->uploadOrganizationProfileImage($request);
        if ($upload_profile_image) {
            return $upload_profile_image;
        } else {
            return false;
        }
    }

    public function uploadOrganizationCoverImage($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember)
    {
        $upload_profile_image = $organizationService->uploadOrganizationCoverImage($request);
        if ($upload_profile_image) {
            return $upload_profile_image;
        } else {
            return false;
        }
    }

    public function createOrganization($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember, $profile_image_path, $cover_image_path)
    {
        $organization = $organizationService->createOrganization($request, $profile_image_path, $cover_image_path);
        if ($organization) {
            return $organization;
        } else {
            return false;
        }
    }

    public function createOrganizationAddress($request, $organizationService, $organizationAddresss, $organizationMember, $profile_image_path, $cover_image_path, $organization_id)
    {
        $organization = $organizationAddresss->createOrganizationAddress($request, $organization_id);
        if ($organization) {
            return $organization;
        } else {
            return false;
        }
    }

    public function organizationAddMemeber($request, $organizationService, $organizationAddresss, $organizationMember, $profile_image_path, $cover_image_path, $organization_id)
    {
        $organization_member = $organizationMember->organizationAddMemeber($request, $organization_id);
        if ($organization_member) {
            return $organization_member;
        } else {
            return false;
        }
    }

    public function view($request, OrganizationService $organizationService, $slug)
    {
        try {
            return $organizationService->view($slug, $request->language);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function existsSlug($slug, $request, $organizationService, $organizationaddresss)
    {
        try {
            $organization = $organizationService->existsSlug($slug);
            if ($organization) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateOrganizationProfileImage($request, $organizationService, $organizationaddresss)
    {
        try {
            $profile_image_path = $organizationService->updateOrganizationProfileImage($request);
            if ($profile_image_path) {
                return $profile_image_path;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateOrganizationCoverImage($request, $organizationService, $organizationaddresss)
    {
        try {
            $cover_images_path = $organizationService->updateOrganizationCoverImage($request);
            if ($cover_images_path) {
                return $cover_images_path;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateOrganization($request, $organizationService, $cover_images_path, $profile_images_path, $slug)
    {
        try {
            $organization = $organizationService->updateOrganization($request, $cover_images_path, $profile_images_path, $slug);
            if ($organization) {
                return $organization;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updatesOrganizationAddress($organization_address, $organizationaddresss, $organization_id)
    {
        try {
            return $organization_address = $organizationaddresss->updatesOrganizationAddress($organization_address, $organization_id);
        } catch (\Exception $e) {
            return $e;

            return false;
        }
    }

    public function list($organizationService, $language)
    {
        try {
            $organization = $organizationService->list($language);
            if ($organization) {
                return $organization;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($slug, $organizationService, $language)
    {
        try {
            $organization = $organizationService->delete($slug, $language);
            if ($organization) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
