<?php

namespace App\Repositories\Api\Manage\LabProgram;

interface LabProgramInterface
{
    public function getLabProgramList($request);

    public function getLabProgramBasedOnSlug($slug);

    public function uploadLabProgramMedia($media);

    public function createLabProgram($request, $upload_media,$upload_achievement_image);
}
