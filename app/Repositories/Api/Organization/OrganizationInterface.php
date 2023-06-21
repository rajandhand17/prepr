<?php

namespace App\Repositories\Api\Organization;

use App\Services\OrganizationAddressService;
use App\Services\OrganizationMemberService;
use App\Services\OrganizationService;

interface OrganizationInterface
{
    public function checkOrganizationExist($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember);

    public function checkOrganizationExistInTrash($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember);

    public function uploadOrganizationProfileImage($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember);

    public function uploadOrganizationCoverImage($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember);

    public function createOrganization($request, OrganizationService $organizationService, OrganizationAddressService $organizationAddresss, OrganizationMemberService $organizationMember, $profile_image_path, $cover_image_path);

    public function createOrganizationAddress($request, $organizationService, $organizationAddresss, $organizationMember, $profile_image_path, $cover_image_path, $organization_id);

    public function organizationAddMemeber($request, $organizationService, $organizationAddresss, $organizationMember, $profile_image_path, $cover_image_path, $organization_id);

    public function view($request, OrganizationService $organizationService, $slug);

    public function existsSlug($slug, $request, $organizationService, $organizationaddresss);

    public function updateOrganizationProfileImage($request, $organizationService, $organizationaddresss);

    public function updateOrganizationCoverImage($request, $organizationService, $organizationaddresss);

    public function updateOrganization($request, $organizationService, $cover_images_path, $profile_images_path, $slug);

    public function updatesOrganizationAddress($organization_address, $organizationaddresss, $organization_id);

    public function delete($slug, $organizationService, $language);

    public function list($organizationService, $language);
}
