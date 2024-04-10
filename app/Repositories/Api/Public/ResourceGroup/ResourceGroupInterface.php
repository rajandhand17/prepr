<?php

namespace App\Repositories\Api\Public\ResourceGroup;

interface ResourceGroupInterface
{
    public function getResourceGroupList($request);

    public function getResourceGroupBasedOnSlug($slug);

    public function getColumnNameValue($action);

    public function checkSocialActivity($resource_group_id, $column, $action);

    public function captureSocialActivity($resource_group_id, $column, $action);
}
