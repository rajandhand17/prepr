<?php

namespace App\Repositories\Api\Project;

interface ProjectInterface
{
    public function getMyProjectIds($userId);

    public function getFavouriteProjectIds($userId);

    public function getInvitedProjectIds($userData);

    public function getProjectList($getProjectIds, $request);

    public function uploadCoverImage($coverImage);

    public function createProject($request, $uploadedCoverMedia);

    public function getProjectBasedOnSlug($slug);

    public function checkNameExistsOrNot($title);

    public function projectPitchTask($projectId, $request);

    public function projectProjectFile($projectId, $request);

    public function updateProject($slug, $request, $uploadedCoverMedia);

    public function addUpdateExternalLink($request, $projectId);

    public function addUpdateAdditionalInfo($request, $projectId);

    public function projectRequirements($projectData);

    public function deleteProject($projectId);

    public function checkProjectRequirementCompleted($projectData);

    public function submitProject($projectData);

    public function getColumnNameValue($action);

    public function checkSocialActivity($projectId, $column, $action);

    public function captureSocialActivity($projectId, $column, $action);
}
