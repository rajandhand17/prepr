<?php

namespace App\Repositories\Api\Dashboard\Organization;

interface OrganizationDashboardInterface
{
    public function getOrganizationList($request);

    public function getLabList($request);

    public function getChallengeList($request);

    public function getMyProjectIds($userId);

    public function getAssessedProjectIds($userData);

    public function getProjectList($getProjectIds, $request);
}
