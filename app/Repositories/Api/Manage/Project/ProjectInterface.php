<?php

namespace App\Repositories\Api\Manage\Project;

interface ProjectInterface
{
    public function getMyProjectIds($userId);

    public function getFavouriteProjectIds($userId);

    public function getInvitedProjectIds($userData);

    public function getProjectList($getProjectIds, $request);

    public function uploadCoverImage($coverImage);

    public function createProject($request, $uploadedCoverMedia);

    public function getProjectChallenges($request);

    public function getProjectLabs($request, $challengeId);

    public function getProjectBasedOnSlug($slug);

    public function getProjectBasedOnUUID($uuid);

    public function checkNameExistsOrNot($title);

    public function projectPitchTask($projectId, $request);

    public function projectProjectFile($projectId, $request);

    public function updateProject($slug, $request, $uploadedCoverMedia);

    public function addUpdateExternalLink($request, $projectId);

    public function addUpdateAdditionalInfo($request, $projectId);

    public function projectRequirements($projectData);

    public function deleteProject($projectId);
}
