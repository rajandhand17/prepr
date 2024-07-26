<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollectionTypeModes;
use App\Models\ResourceModuleTypeModes;

class ResourceCollectionTypeModesService
{
    public static function getResourceCollectionBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
            return ResourceCollectionTypeModes::where(['type_mode'=>config('constants.resource_mode.type'), 'value'=>config('constants.resource_types.'.$type)])->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
        // Storing type's and mode's
    public function createResourceCollectionTypeModes($request, $resourceCollectionId)
    {
        try {
            if ($request->has('type')) {
                $value = config('constants.resource_types.' . $request->type);
                $resourceCollectionType = new ResourceCollectionTypeModes();
                $resourceCollectionType->resource_collection_id = $resourceCollectionId;
                $resourceCollectionType->type_mode = config('constants.resource_mode.type');
                $resourceCollectionType->value = $value;
                $resourceCollectionType->Save();
            }
            if ($request->has('mode')) {
                $value = config('constants.resource_mode_type.' . $request->mode);
                $resourceCollectionMode = new ResourceCollectionTypeModes();
                $resourceCollectionMode->resource_collection_id = $resourceCollectionId;
                $resourceCollectionMode->type_mode = config('constants.resource_mode.mode');
                $resourceCollectionMode->value = $value;
                $resourceCollectionMode->Save();
            }
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
    // Updating type's and mode's
    public function updateResourceCollectionTypeModes($request, $resourceCollectionId)
    {
        try {
            if ($request->has('type') && !empty($request->type)) {
                $value = config('constants.resource_types.' . $request->type);
                ResourceCollectionTypeModes::updateOrCreate([
                    'resource_collection_id' => $resourceCollectionId,
                    'type_mode'          => config('constants.resource_mode.type'),
                ], [
                    'value'              => $value,
                ]);
            }
            if ($request->has('mode')) {
                $value = config('constants.resource_mode_type.' . $request->mode);
                ResourceCollectionTypeModes::updateOrCreate([
                    'resource_collection_id' => $resourceCollectionId,
                    'type_mode'          => config('constants.resource_mode.mode'),
                ], [
                    'value'              => $value,
                ]);
            }
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function cloneResourceCollectionTypeModes($originalResourceCollectionAssociation, $clonedResourceCollectionId)
    {
        try {
            if($originalResourceCollectionAssociation){
                $cloneResourceModuleSKills = $originalResourceCollectionAssociation->replicate();
                $cloneResourceModuleSKills->resource_collection_id = $clonedResourceCollectionId;
                $cloneResourceModuleSKills->save();
            }
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
