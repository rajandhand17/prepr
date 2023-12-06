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

    public function deleteResourceModuleMedia($request, $resource_module_id)
    {
        try {
            switch ($request->type) {
                case 'document':
                    $type = config('constants.resource_module_type.document');
                    break;
                case 'video':
                    $type = config('constants.resource_module_type.video');
                    break;
                case 'audio':
                    $type = config('constants.resource_module_type.audio');
                    break;
                case 'embedded_video':
                    $type = config('constants.resource_module_type.embedded_video');
                    break;
                case 'embedded_audio':
                    $type = config('constants.resource_module_type.embedded_audio');
                    break;
                case 'url':
                    $type = config('constants.resource_module_type.url');
                    break;
                case 'image':
                    $type = config('constants.resource_module_type.image');
                    break;
                case 'embedded_cover_video':
                    $type = config('constants.resource_module_type.Embedded_Cover_Video');
                    break;
                default:
                    $type = config('constants.resource_module_type.image');
                    break;
            }
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

    public function addLinks($request, $resource_module_id)
    {
        try {
            foreach ($request->links as  $value) {
                $type = config('constants.resource_module_type.url');
                $resourceModuleDetailed = self::insertRecords($resource_module_id, $value['title'], $type, $value['path'], $value['social_link_id']);
                if (!$resourceModuleDetailed) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addEmbeddedMedia($request, $resource_module_id)
    {
        try {
            foreach ($request->embed_media as $key => $value) {
                switch ($value['type']) {
                    case 'embedded_video':
                        $type = config('constants.resource_module_type.embedded_video');
                        $title = $value['type'];
                        break;
                    case 'embedded_audio':
                        $type = config('constants.resource_module_type.embedded_audio');
                        $title = $value['type'];
                        break;
                    default:
                        $type = '';
                }
                $resourceModuleDetailed = self::insertRecords($resource_module_id, $title, $type, $value['path'], null);
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
