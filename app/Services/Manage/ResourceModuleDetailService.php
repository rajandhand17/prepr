<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Models\ResourceModuleDetail;

class ResourceModuleDetailService
{
    public function insertRecords($resource_module_id, $title, $type, $path, $social_link_id)
    {
        try {
            $resourceModuleDetailed = new ResourceModuleDetail();
            $resourceModuleDetailed->resource_module_id = $resource_module_id;
            $resourceModuleDetailed->title = $title;
            $resourceModuleDetailed->type = $type;
            $resourceModuleDetailed->path = $path;
            $resourceModuleDetailed->social_link_id = $social_link_id;
            $resourceModuleDetailed->save();

            return $resourceModuleDetailed;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addLinks($request,$resource_module_id)
    {
        try {
            foreach ($request->title as $key => $value) {
                $type = config('constants.resource_module_type.url');
               $resourceModuleDetailed = self::insertRecords($resource_module_id, $value,$type,$request->path[$key], $request->social_link_id[$key]);
                if (!$resourceModuleDetailed) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function fileUpload($request, $resource_module_id, $type)
    {
        try {
            foreach ($request->file_upload as $file) {
                $upload_resource_module_cover_image = FileUploadHelper::uploadImageToS3($file, 'resource_module');
                if ($upload_resource_module_cover_image == false) {
                    return false;
                }
                $imagePath = explode('/', $upload_resource_module_cover_image);
                $resourceModuleDetailed = self::insertRecords($resource_module_id, $imagePath[count($imagePath) - 1], $type, $upload_resource_module_cover_image, null);
                if (!$resourceModuleDetailed) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteResourceModuleDetail($resource_module_id)
    {
        try {
            ResourceModuleDetail::where('resource_module_id', $resource_module_id)->delete();

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deleteMedia($request, $resource_module_id, $type)
    {
        try {
            ResourceModuleDetail::where([
                'id'                 => $request->media_id,
                'resource_module_id' => $resource_module_id,
                'type'               => $type,
            ])->delete();

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addEmbedMedia($request, $resource_module_id)
    {
        try {
            foreach ($request->type as $key => $value) {
                switch ($value) {
                    case 'embedded_video' || 'video':
                        $type = config('constants.resource_module_type.embedded_video');
                        $title = $value;
                        break;
                    case 'embedded_audio' || 'audio':
                        $type = config('constants.resource_module_type.embedded_audio');
                        $title = $value;
                        break;
                    default:
                        $type = '';
                }
                $resourceModuleDetailed = self::insertRecords($resource_module_id, $title, $type, $request->path[$key], null);
                if (!$resourceModuleDetailed) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
