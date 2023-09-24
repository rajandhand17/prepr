<?php

namespace App\Repositories\Api\Manage\ResourceModuleDetail;

interface ResourceModuleDetailInterface
{
    public function addLinks($request,$resource_module_id,$type);

}
