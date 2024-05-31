<?php

namespace App\Services\Public;

use App\Models\ResourceModuleDetail;
use App\Models\ResourceModuleVisit;
use Exception;

class ResourceModuleDetailService
{
    public function checkResourceModuleAsset($resourceModuleId, $assetId)
    {
        try {
            $checkResourceModuleAsset = ResourceModuleDetail::where(['id' => $assetId, 'resource_module_id' => $resourceModuleId])->first();

            return $checkResourceModuleAsset;
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType)
    {
        try {
            switch ($assetType) {
                case '0':
                    $file_type = config('constants.file_type.document');
                    break;
                case '1':
                    $file_type = config('constants.file_type.video');
                    break;
                case '2':
                    $file_type = config('constants.file_type.audio');
                    break;
                case '3':
                    $file_type = config('constants.file_type.embedded');
                    break;
                case '4':
                    $file_type = config('constants.file_type.embedded_audio');
                    break;
                case '5':
                    $file_type = config('constants.file_type.url');
                    break;
                case '6':
                    $file_type = config('constants.file_type.image');
                    break;
            }
            $checkResourceModuleAssetVisit = ResourceModuleVisit::where(['user_id' => $userId, 'module_id' => $resourceModuleId, 'module_asset_id' => $assetId, 'asset_type' => $file_type])->exists();

            return $checkResourceModuleAssetVisit;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType)
    {
        try {
            switch ($assetType) {
                case '0':
                    $file_type = config('constants.file_type.document');
                    break;
                case '1':
                    $file_type = config('constants.file_type.video');
                    break;
                case '2':
                    $file_type = config('constants.file_type.audio');
                    break;
                case '3':
                    $file_type = config('constants.file_type.embedded');
                    break;
                case '4':
                    $file_type = config('constants.file_type.embedded_audio');
                    break;
                case '5':
                    $file_type = config('constants.file_type.url');
                    break;
                case '6':
                    $file_type = config('constants.file_type.image');
                    break;
            }

            $checkResourceModuleAssetVisit = ResourceModuleVisit::create(['user_id' => $userId, 'module_id' => $resourceModuleId, 'module_asset_id' => $assetId, 'asset_type' => $file_type]);
            if ($checkResourceModuleAssetVisit) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
