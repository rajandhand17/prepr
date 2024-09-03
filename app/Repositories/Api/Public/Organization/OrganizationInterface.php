<?php

namespace App\Repositories\Api\Public\Organization;

interface OrganizationInterface
{
    public function getList($request);

    public function getOrganizationBasedOnSlug($slug);

    public function getColumnNameValue($action);

    public function checkSocialActivity($organization_id, $column, $action);

    public function captureSocialActivity($organization_id, $column, $action);
}
