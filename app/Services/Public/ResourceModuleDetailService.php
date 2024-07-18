<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType)
    {
        try {
            switch ($assetType) {
                case '0':
                    $file_type = config('constants.visit_type_id.document');
                    break;
                case '1':
                    $file_type = config('constants.visit_type_id.video');
                    break;
                case '2':
                    $file_type = config('constants.visit_type_id.audio');
                    break;
                case '3':
                    $file_type = config('constants.visit_type_id.embedded');
                    break;
                case '4':
                    $file_type = config('constants.visit_type_id.embedded_audio');
                    break;
                case '5':
                    $file_type = config('constants.visit_type_id.url');
                    break;
                case '6':
                    $file_type = config('constants.visit_type_id.image');
                    break;
                case '7':
                    $file_type = config('constants.visit_type_id.scrom');
                    break;
                case '8':
                    $file_type = config('constants.visit_type_id.go1');
                    break;
            }
            $checkResourceModuleAssetVisit = ResourceModuleVisit::where(['user_id' => $userId, 'module_id' => $resourceModuleId, 'module_asset_id' => $assetId, 'asset_type' => $file_type])->exists();

            return $checkResourceModuleAssetVisit;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addResourceModuleAssetVisit($userId, $resourceModuleId, $assetId, $assetType)
    {
        try {
            switch ($assetType) {
                case '0':
                    $file_type = config('constants.visit_type_id.document');
                    break;
                case '1':
                    $file_type = config('constants.visit_type_id.video');
                    break;
                case '2':
                    $file_type = config('constants.visit_type_id.audio');
                    break;
                case '3':
                    $file_type = config('constants.visit_type_id.embedded');
                    break;
                case '4':
                    $file_type = config('constants.visit_type_id.embedded_audio');
                    break;
                case '5':
                    $file_type = config('constants.visit_type_id.url');
                    break;
                case '6':
                    $file_type = config('constants.visit_type_id.image');
                    break;
                case '7':
                    $file_type = config('constants.visit_type_id.scrom');
                    break;
                case '8':
                    $file_type = config('constants.visit_type_id.go1');
                    break;
            }

            $checkResourceModuleAssetVisit = ResourceModuleVisit::create(['user_id' => $userId, 'module_id' => $resourceModuleId, 'module_asset_id' => $assetId, 'asset_type' => $file_type]);
            if ($checkResourceModuleAssetVisit) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkResourceAssetCompletedOrNot($userId, $assetId)
    {
        try {
            $checkResourceModuleAssetVisit = ResourceModuleVisit::where(['user_id' => $userId, 'module_asset_id' => $assetId])->exists();
            if ($checkResourceModuleAssetVisit) {
                return 'yes';
            }

            return 'no';
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
