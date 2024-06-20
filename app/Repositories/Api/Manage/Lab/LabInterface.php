<?php

namespace App\Repositories\Api\Manage\Lab;

interface LabInterface
{
    public function getLabCountBasedOnOrganization($organizationId);

    public function getLabList($request, $organization);

    public function getLabBasedOnSlug($slug);

    public function uploadLabCoverImage($image);

    public function createLab($request, $upload_profile_image, $upload_achievements_image, $organizationData);

    public function updateLab($slug, $request, $upload_cover_image, $upload_achievement_image, $organizationData);

    public function deleteLab($lab_id, $request);

    public function checkSlug($slug);

    public function checkNameExistsOrNot($title);

    public function getLabListName($request, $organization);

    public function createLabUsingAIPreview($request);

    public function createLabUsingAI($request, $upload_profile_image, $upload_achievements_image);
}
