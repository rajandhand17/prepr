<?php

namespace App\Services\Manage;


use App\Helpers\FileUploadHelper;
use App\Models\ResourceModule;
use App\Models\ResourceModuleDetail;

class ResourceModuleDetailService
{
   public function insertRecords($resource_module_id,$title,$type,$path,$social_link_id){
        try{
            $resourceModuleDetailed=new ResourceModuleDetail();
            $resourceModuleDetailed->resource_module_id=$resource_module_id;
            $resourceModuleDetailed->title=$title;
            $resourceModuleDetailed->type=$type;
            $resourceModuleDetailed->path=$path;
            $resourceModuleDetailed->social_link_id=$social_link_id;
            $resourceModuleDetailed->save();
            return $resourceModuleDetailed;
        }catch (\Exception $e){
            return false;
        }
    }

    public function addLinks($resource_module_id,$title,$type,$path,$social_link_id){
       try{
           foreach ($title as $key => $value){
               $resourceModuleDetailed=self::insertRecords($resource_module_id,$value,$type[$key],$path[$key],$social_link_id[$key]);
               if(!$resourceModuleDetailed){
                   return false;
               }
           }
           return true;
       }catch (\Exception $e){
           return false;
       }
    }
    public function fileUpload($request,$resource_module_id,$type){
        try{
            foreach ($request->file_upload as $file){
                $upload_resource_module_cover_image= FileUploadHelper::uploadImageToS3($file, 'resource_module');
                if ($upload_resource_module_cover_image == false){
                    return false;
                }
                $imagePath=explode('/',$upload_resource_module_cover_image);
                $resourceModuleDetailed=self::insertRecords($resource_module_id,$imagePath[count($imagePath)-1],$type,$upload_resource_module_cover_image,null);
                if(!$resourceModuleDetailed){
                    return false;
                }
            }
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function deleteResourceModuleDetail($resource_module_id)
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
