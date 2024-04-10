<?php

namespace App\Repositories\Api\ProjectMemberManagement;

interface ProjectMemberManagementInterface
{
    public function getRoles();

    public function getProjectBasedParticipants($projectData, $request);

    public function getTemplate($requestLang);

    public function downloadSample();

    public function addParticipates($projectData, $request);

    public function checkProjectJoinUnjoinStatus($userEmail, $projectData);

    public function acceptOrRejectProjectJoinRequest($request, $projectData, $action);

    public function checkParticipantsUUID($projectId, $uuid);

    public function checkCurrentProjectRole($projectId, $uuid, $role);

    public function updateProjectRole($projectId, $uuid, $role);

    public function deleteParticipates($projectData, $request);

    public function checkParticipantProjectJoinUnjoinStatus($userEmail, $projectData);

    public function participantAcceptOrRejectJoinRequest($userEmail, $projectData, $action);
}
