<?php

namespace App\Repositories\Api\Manage\Organization;

use App\Models\Organization;

interface OrganizationInterface
{
    public function getOrganizationList($request);

    public function getOrganizationBasedOnSlug($slug);

    public function checkOrganizationExistBasedOnTitle($request);

    public function checkOrganizationExistInTrashBasedOnTitle($request);

    public function uploadOrganizationProfileImage($request);

    public function uploadOrganizationCoverImage($request);

    public function createOrganization($request, $profile_image_path, $cover_image_path);

    public function createOrganizationAddress($request, $organization_id);

    public function createOrganizationMembers($request, $organization_id);

    public function createOrganizationExternalLinks($request, $organizationId);

    public function updateOrganization($request, $cover_images_path, $profile_images_path, $slug);

    public function updatesOrganizationAddress($request, $organization_id);

    public function updatesOrganizationMembers($request, $organization_id);

    public function updateOrganizationExternalLinks($request, $organizationId);

    public function deleteOrganization($organizationData, $request);

    public function checkSlug($slug);

    public function getOrganizationListOnlyNameAndUuid($request);

    public function selectPlan($organization, $request);

    public function planData($organizationData);

    public function updateOrganizationCustomLoginRegistration($request, $organizationData);

    public function incrementView(Organization $organization);

    public function checkOrganizationCustomizationData($slug);
}
