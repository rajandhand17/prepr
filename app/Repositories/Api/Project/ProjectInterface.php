<?php

namespace App\Repositories\Api\Project;

interface ProjectInterface
{
    public function getMyProjectIds($userId);

    public function getFavouriteProjectIds($userId);

    public function getAcceptedInvitesProjectIds($userData);

    public function getPendingInvitesProjectIds($userData);

    public function getAssessedProjectIds($userData);

    public function getPendingProjectIds($userData);

    public function getProjectList($getProjectIds, $request);

    public function uploadCoverImage($coverImage);

    public function checkProjectCreatedWithChallenge($challengeId);

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

    public function checkSubmisstionDate($projectData);

    public function submitProject($projectData, $checkLateSubmission, $request);

    public function getColumnNameValue($action);

    public function checkSocialActivity($projectId, $column, $action);

    public function captureSocialActivity($projectId, $column, $action);

    public function checkAssessmentChallenges($userData);

    public function captureProjectAssessment($projectData, $userData, $request);

    public function captureProjectAIAssessment($projectData, $userData, $request);

    public function assessProjectAI($request);

    public function addUpdateProjectSkillsRecruitingStatus($projectId, $request);

    public function checkChallengeProjectAssessment($projectDataId, $userData);

    public function deleteChallengeProjectAssessment($projectDataId, $userData);

    public function deleteProjectMedia($request, $projectDataId);

    public function storeHistory($projectId, $userId, $activity);

    public function fetchProjectHistory($projectId);

    public function checkProjectJoinedStatus($projectId, $userEmail);

    public function joinProject($projectId, $userEmail);

    public function unJoinProject($projectId, $userEmail);
}
