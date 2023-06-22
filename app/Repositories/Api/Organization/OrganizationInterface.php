<?php

namespace App\Repositories\Api\Organization;

use App\Services\OrganizationAddressService;
use App\Services\OrganizationMemberService;
use App\Services\OrganizationService;

interface OrganizationInterface
{
    public function checkOrganizationExist($request);

    public function checkOrganizationExistInTrash($request);

    public function uploadOrganizationProfileImage($request);

    public function uploadOrganizationCoverImage($request);

    public function createOrganization($request, $profile_image_path, $cover_image_path);

    public function createOrganizationAddress($request, $organization_id);

    public function organizationAddMemeber($request, $organization_id);

    public function view($request, $slug);

    public function checkSlug($slug);

    public function updateOrganizationProfileImage($request);

    public function updateOrganizationCoverImage($request);

    public function updateOrganization($request, $cover_images_path, $profile_images_path, $slug);

    public function updatesOrganizationAddress($organization_address, $organization_id);

    public function delete($slug, $language);

    public function list($language);
}
