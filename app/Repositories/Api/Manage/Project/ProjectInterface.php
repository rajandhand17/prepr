<?php

namespace App\Repositories\Api\Manage\Project;

interface ProjectInterface
{
    public function uploadCoverImage($coverImage);

    public function createProject($request, $uploadedCoverImage);

    public function getProjectChallenges($request);

    public function getProjectLabs($request, $challengeId);

    public function getProjectBasedOnSlug($slug);

    public function getProjectBasedOnUUID($uuid);

    public function checkNameExistsOrNot($title);

    public function projectPitchTask($projectId, $request);

    public function projectProjectFile($projectId, $request);
}
