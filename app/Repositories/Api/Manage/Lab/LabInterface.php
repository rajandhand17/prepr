<?php

namespace App\Repositories\Api\Manage\Lab;

interface LabInterface
{
    public function createLab($request, $upload_profile_image, $upload_acheivements_image);

    public function uploadCoverImage($image);

    public function getLabDetails($slug);

    public function checkSlug($slug);

    public function checkNameExistsOrNot($slug);

    public function updateLab($slug, $request, $upload_profile_image, $upload_acheivements_image);

    public function deleteLab($lab_id, $request);

    public function updateCoverImage($image);

    public function checkActivity($activity, $lab_id);

    public function storeLabActivity($activity, $lab_id, $request);
}
