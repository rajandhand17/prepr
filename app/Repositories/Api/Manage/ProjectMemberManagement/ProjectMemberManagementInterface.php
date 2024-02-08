<?php

namespace App\Repositories\Api\Manage\ProjectMemberManagement;

interface ProjectMemberManagementInterface
{
    public function getProjectBasedParticipants($projectData, $request);

    public function getTemplate($requestLang);

    public function downloadSample();

    public function addParticipates($projectData, $request);

    public function checkProjectJoinUnjoinStatus($request, $projectData);

    public function acceptOrRejectProjectJoinRequest($request, $projectData, $action);

    public function deleteParticipates($projectData, $request);
}
