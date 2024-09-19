<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleDetail;
use App\Models\ResourceModuleVisit;
use App\Models\ScormScoTracking;
use Exception;
use Illuminate\Support\Facades\Log;

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateRecords($existsResourceModule, $resource_module_id, $title, $type, $path, $social_link_id)
    {
        try {
            $existsResourceModule->resource_module_id = $resource_module_id;
            $existsResourceModule->title = $title;
            $existsResourceModule->type = $type;
            $existsResourceModule->path = $path;
            $existsResourceModule->social_link_id = $social_link_id;
            $existsResourceModule->save();

            return $existsResourceModule;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fileUpload($request, $resource_module_id)
    {
        try {
            if (isset($request->file_upload) && !empty($request->file_upload)) {
                foreach ($request->file_upload as $file_upload) {
                    if (false !== mb_strpos($file_upload->getMimeType(), 'image')) {
                        $file_type = config('constants.file_type.image');
                        $uploaded_file_path = FileUploadHelper::uploadImageToS3($file_upload, 'resource_file');
                    } elseif (false !== mb_strpos($file_upload->getMimeType(), 'video')) {
                        $file_type = config('constants.file_type.video');
                        $uploaded_file_path = FileUploadHelper::uploadVideoToS3($file_upload, 'resource_file');
                    } elseif (false !== mb_strpos($file_upload->getMimeType(), 'audio')) {
                        $file_type = config('constants.file_type.audio');
                        $uploaded_file_path = FileUploadHelper::uploadDocToS3($file_upload, 'resource_file');
                    } else {
                        $file_type = config('constants.file_type.document');
                        $uploaded_file_path = FileUploadHelper::uploadDocToS3($file_upload, 'resource_file');
                    }
                    if ($uploaded_file_path == false) {
                        return false;
                    }
                    $storeData = self::insertRecords($resource_module_id, $file_upload->getClientOriginalName(), $file_type, $uploaded_file_path, null);
                    if (!$storeData) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteResourceModuleDetail($resource_module_id)
    {
        try {
            ResourceModuleDetail::where('resource_module_id', $resource_module_id)->delete();

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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

            self::deleteResourceModuleDetailVisitBasedOnAssetId($request->media_id, $resource_module_id);

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function addLinks($request, $resource_module_id)
    {
        try {
            foreach ($request->links as  $value) {
                $type = config('constants.resource_module_type.url');
                $checkExistsResourceModules = self::checkDuplicateLinks($value['path'], $value['social_link_id'], $resource_module_id);

                if (!$checkExistsResourceModules) {
                    $resourceModuleDetailed = self::insertRecords($resource_module_id, $value['title'], $type, $value['path'], $value['social_link_id']);
                }else{
                    $resourceModuleDetailed = self::updateRecords($checkExistsResourceModules, $resource_module_id, $value['title'], $type, $value['path'], $value['social_link_id']);
                }

                if (!$resourceModuleDetailed) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkDuplicateLinks($path, $social_link_id, $resource_module_id)
    {
        try {
            return ResourceModuleDetail::where(['resource_module_id'=>$resource_module_id, 'path'=>$path, 'social_link_id'=>$social_link_id])->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkDuplicateEmbeddedMedia($id, $resource_module_id)
    {
        try {
            return ResourceModuleDetail::where(['resource_module_id'=>$resource_module_id, 'id'=>$id])->first();
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

                $checkExistsResourceModules = self::checkDuplicateEmbeddedMedia($value['id'], $resource_module_id);

                if (!$checkExistsResourceModules) {
                    $resourceModuleDetailed = self::insertRecords($resource_module_id, $title, $type, $value['path'], null);
                 }else{
                    $resourceModuleDetailed = self::updateRecords($checkExistsResourceModules, $resource_module_id, $title, $type, $value['path'], null);
                }

                if (!$resourceModuleDetailed) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createResourceModuleDetailsAI($request, $resource_module_id)
    {
        try {
            if (!isset($request['resource_module_items']) || !is_array($request['resource_module_items'])) {
                Log::error('Error in createResourceModuleDetailsAI in ResourceModuleDetailService.php: resource_module_items is neither set nor an array!');

                return false;
            }

            foreach ($request['resource_module_items'] as $item) {
                $type = ($item['type'] == 'link') ? '5' : ((isset($item['embedHTML']) && !empty($item['embedHTML'])) ? '3' : '1');
                $resourceDetail = new ResourceModuleDetail([
                    'title'              => $item['title'],
                    'path'               => (isset($item['embedHTML']) && !empty($item['embedHTML'])) ? $item['embedHTML'] : $item['url'],
                    'resource_module_id' => $resource_module_id,
                    'type'               => $type,
                ]);

                if (!$resourceDetail->save()) {
                    Log::error('Error in createResourceModuleDetailsAI in ResourceModuleDetailService.php: Failed to save resource detail for title: '.$item['title']);

                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createResourceModuleDetailsAI in ResourceModuleDetailService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function deleteResourceModuleDetailVisit($resource_module_id)
    {
        try {
            ResourceModuleVisit::where('module_id', $resource_module_id)->delete();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteResourceModuleDetailVisitBasedOnAssetId($assetId, $resource_module_id)
    {
        try {
            ResourceModuleVisit::where(['module_id' => $resource_module_id, 'module_asset_id' => $assetId])->delete();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkResourceScormCompletedOrNot($userId, $scoId)
    {
        try {
            return ScormScoTracking::query()
                ->where('user_id', $userId)
                ->where('sco_id', $scoId)
                ->where(function ($query) {
                    return  $query->where('lesson_status', 'passed')
                    ->orWhere('completion_status', 'completed');
                })
                ->exists();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
