<?php

namespace App\Repositories\Api\Dashboard\Lab;

interface LabDashboardInterface
{
    public function fetchChallengeReportBasedOnOrganization($organizationId);

    public function fetchLabReportBasedOnOrganization($organizationId);

    public function fetchResourceReportBasedOnOrganization($organizationId);

    public function fetchProjectReportBasedOnOrganization($organizationId);

    public function checkOrganizationPlan($organizationData);
}
