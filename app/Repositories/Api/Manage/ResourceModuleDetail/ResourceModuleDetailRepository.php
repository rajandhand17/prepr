<?php

namespace App\Repositories\Api\Manage\ResourceModuleDetail;

use App\Services\Manage\ResourceModuleDetailService;
use App\Services\Manage\ResourceModuleService;

class ResourceModuleDetailRepository implements ResourceModuleDetailInterface
{

    protected $resourceModuleDetailsService;

    public function __construct(ResourceModuleDetailService $resourceModuleDetailsService)
    {
        $this->resourceModuleDetailsService = $resourceModuleDetailsService;
    }

    public function addLinks($request,$resource_module_id,$type){
        try{
            return $this->resourceModuleDetailsService->addLinks($request,$resource_module_id,$type);
        }catch(\Exception $e) {
            return false;
        }
    }

    public function uploadResouceModuleFileUpload($files){
        try{
            return $this->resourceModuleDetailsService->uploadResouceModuleFile($files);
        }catch(\Exception $e) {
            return false;
        }
    }

    public function insertData($uploaded_media,$resource_module_id,$type){
        try{
            return $this->resourceModuleDetailsService->insertData($uploaded_media,$resource_module_id,$type);
        }catch(\Exception $e){
            return false;
        }
    }
}
