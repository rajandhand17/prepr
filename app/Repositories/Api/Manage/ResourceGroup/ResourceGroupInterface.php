<?php

namespace App\Repositories\Api\Manage\ResourceGroup;

interface ResourceGroupInterface
{
    public function createResourceGroup($request, $upload_cover_image, $upload_achievement_image);

    public function uploadResourceGroupCoverImage($cover_image);

    public function uploadAchievementImage($achievement_image);

    public function getResourceGroupBasedOnSlug($slug);
}
