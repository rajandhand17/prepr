<?php

namespace App\Repositories\Api\Manage\Lab;

interface LabInterface
{
    public function getLabList($request,$organization);

    public function getLabBasedOnSlug($slug);

    public function uploadLabCoverImage($image);

    public function createLab($request, $upload_profile_image, $upload_achievement_image);

    public function updateLab($slug, $request, $upload_profile_image, $upload_achievement_image);

    public function deleteLab($lab_id, $request);

    public function checkSlug($slug);

    public function checkNameExistsOrNot($slug);
}
