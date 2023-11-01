<?php

namespace App\Repositories\Api\Public\ResourceGroup;

interface ResourceGroupInterface
{
    public function getResourceGroupList($request);

    public function getResourceGroupBasedOnSlug($slug);
}
