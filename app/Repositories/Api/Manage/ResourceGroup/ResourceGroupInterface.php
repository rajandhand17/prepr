<?php

namespace App\Repositories\Api\Manage\ResourceGroup;

interface ResourceGroupInterface
{
    public function getResourceGroupCountBasedOnOrganization($organizationId);

    public function createResourceGroup($request, $upload_cover_image, $upload_achievement_image, $organizationId,$labId,$challengeId);

    public function uploadResourceGroupCoverImage($cover_image);

    public function uploadAchievementImage($achievement_image);

    public function getResourceGroupBasedOnSlug($slug);

    public function deleteGroupModule($checkResourceGroupId);

    public function checkName($slug);

    public function updateResourceGroup($slug, $request, $upload_cover_image, $upload_achievement_image, $organizationId,$labId,$challengeId);

    public function getResourceGroupList($request, $organization);

    public function getResourceGroupListName($request, $organization);
}
