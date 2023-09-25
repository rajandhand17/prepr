<?php

namespace App\Repositories\Api\Manage\ResourceModuleDetail;

interface ResourceModuleDetailInterface
{
    public function addLinks($request,$resource_module_id,$type);
    public function insertData($uploaded_media,$resource_module_id,$type);
    public function uploadResouceModuleFileUpload($files);

}
