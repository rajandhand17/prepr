<?php

namespace App\Repositories\Api\Manage\Project;

interface ProjectInterface
{
    public function uploadCoverImage($coverImage);

    public function createProject($request, $uploadedCoverImage);
}
