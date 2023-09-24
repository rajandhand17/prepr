<?php

namespace App\Services\Manage;


use App\Helpers\FileUploadHelper;
use App\Models\ResourceModule;
use App\Models\ResourceModuleDetail;

class ResourceModuleDetailService
{
    public function addLinks($request,$resource_module_id,$type){
        try {
            $resourceModuleDetailed=new ResourceModuleDetail();
            $resourceModuleDetailed->resource_module_id=$resource_module_id;
            $resourceModuleDetailed->title=$request->title;
            $resourceModuleDetailed->type=$type;
            $resourceModuleDetailed->path=$request->path;
            $resourceModuleDetailed->social_link_id=$request->social_link_id;
            $resourceModuleDetailed->save();
            return $resourceModuleDetailed;
        }catch (\Exception $e){
            return false;
        }
    }

    public function uploadResouceModuleFile($image){
        try{
            $upload_resource_module_cover_image = FileUploadHelper::uploadImageToS3($image, 'resource_module');
            if ($upload_resource_module_cover_image == false){
                return false;
            }
            return $upload_resource_module_cover_image;
        }catch (\Exception $e){
            return false;
        }
    }

    public function insertData($uploaded_media,$resource_module_id,$type){
        try{
            $resourceModuleDetailed=new ResourceModuleDetail();
            $resourceModuleDetailed->resource_module_id=$resource_module_id;
            $resourceModuleDetailed->title="image";
            $resourceModuleDetailed->type=$type;
            $resourceModuleDetailed->path=$uploaded_media;
            $resourceModuleDetailed->save();
            return $resourceModuleDetailed;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function delete($resource_module_id)
    {
        try {
            $resourceModuleDetail=ResourceModuleDetail::where('resource_module_id', $resource_module_id)->first();
            if($resourceModuleDetail!==null){
                $resourceModuleDetail=ResourceModuleDetail::where('resource_module_id', $resource_module_id)->delete();
            }
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}
