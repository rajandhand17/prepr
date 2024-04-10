<?php

namespace App\Repositories\Api\Dashboard\Lab;

interface LabDashboardInterface
{
    public function getLabList($request);

    public function getChallengeList($request);

    public function getMyProjectIds($userId);

    public function getAssessedProjectIds($userData);

    public function getProjectList($getProjectIds, $request);
}
