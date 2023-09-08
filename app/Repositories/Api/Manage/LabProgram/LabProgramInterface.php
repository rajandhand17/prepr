<?php

namespace App\Repositories\Api\Manage\LabProgram;

interface LabProgramInterface
{
    public function getLabProgramList($request, $organization);

    public function getLabProgramBasedOnSlug($slug);

    public function uploadLabProgramMedia($media);

    public function createLabProgram($request, $upload_media, $upload_achievement_image);

    public function updateLabProgram($slug, $request, $upload_media, $upload_achievement_image);

    public function checkSlug($slug);

    public function delete($slug);

    public function checkNameExistsOrNot($title);
}
