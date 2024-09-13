<?php

namespace App\Repositories\Api\Public\Organization;

use App\Models\Organization;

interface OrganizationInterface
{
    public function getList($request);

    public function getOrganizationBasedOnSlug($slug);

    public function getColumnNameValue($action);

    public function checkSocialActivity($organization_id, $column, $action);

    public function captureSocialActivity($organization_id, $column, $action);

    public function incrementView(Organization $organization);
}
