<?php

namespace App\Repositories\Api\Public\ResourceModule;

interface ResourceModuleInterface
{
    public function getResourceModuleList($request);
    public function getResourceModuleBasedOnSlug($slug);

    public function checkslug($slug);

    public function checkReview($resource_module_id,$request);

    public function addReview($resource_module_id,$request);
}
