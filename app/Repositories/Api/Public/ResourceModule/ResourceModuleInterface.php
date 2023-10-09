<?php

namespace App\Repositories\Api\Public\ResourceModule;

interface ResourceModuleInterface
{
    public function getResourceModuleList($request);

    public function getResourceModuleBasedOnSlug($slug);

    public function checkSlug($slug);

    public function checkRating($resource_module_id, $request);

    public function addRating($resource_module_id, $request);

    public function getColumnNameValue($action);
    public function checkSocialActivity($resource_module_id, $column, $action);

    public function captureSocialActivity($resource_module_id, $column, $action);
}
