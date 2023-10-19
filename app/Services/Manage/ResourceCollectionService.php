<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;

class ResourceCollectionService
{
    public static function createResourceCollection($request,$upload_media){
        try {
            
        }catch (\Exception $e){
            return false;
        }
    }

    public static function uploadResourceCollectionCoverImage($cover_image){
        try {
            $upload_resource_collection_cover_image = FileUploadHelper::uploadImageToS3($cover_image, 'resource_collection');
            if ($upload_resource_collection_cover_image == false) {
                return false;
            }
            return $upload_resource_collection_cover_image;
        }catch (\Exception $e){
            return false;
        }
    }
}
