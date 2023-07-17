<?php

namespace App\Repositories\Api\Manage\Organization;

interface OrganizationInterface
{
    public function getOrganizationList($request);

    public function getOrganizationExistBasedOnSlug($slug);

    public function checkOrganizationExistBasedOnTitle($request);

    public function checkOrganizationExistInTrashBasedOnTitle($request);

    public function uploadOrganizationProfileImage($request);

    public function uploadOrganizationCoverImage($request);

    public function createOrganization($request, $profile_image_path, $cover_image_path);

    public function organizationAddAddress($request, $organization_id);

    public function organizationAddMembers($request, $organization_id);

    public function getOrganization($request, $slug);

    public function checkSlug($slug);

    public function updateOrganizationProfileImage($request);

    public function updateOrganizationCoverImage($request);

    public function updateOrganization($request, $cover_images_path, $profile_images_path, $slug);

    public function updatesOrganizationAddress($request, $organization_id);

    public function updatesOrganizationMembers($organization_address, $organization_id);

    public function deleteOrganization($slug, $language);

}
