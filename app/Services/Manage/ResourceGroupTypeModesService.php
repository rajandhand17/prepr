<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceCollectionTypeModes;
use App\Models\ResourceGroupTypeModes;

class ResourceGroupTypeModesService
{
    public static function getResourceGroupBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
            return ResourceGroupTypeModes::where(['type_mode'=>config('constants.resource_mode.type'), 'value'=>config('constants.resource_types.'.$type)])->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // Storing type's and mode's
    public function createResourceGroupTypeModes($request, $resourceGroupId)
    {
        try {
            if ($request->has('type')) {
                $value = config('constants.resource_types.' . $request->type);
                $resourceGroupType = new ResourceGroupTypeModes();
                $resourceGroupType->resource_group_id = $resourceGroupId;
                $resourceGroupType->type_mode = config('constants.resource_mode.type');
                $resourceGroupType->value = $value;
                $resourceGroupType->Save();
            }
            if ($request->has('mode')) {
                $value = config('constants.resource_mode_type.' . $request->mode);
                $resourceGroupMode = new ResourceGroupTypeModes();
                $resourceGroupMode->resource_group_id = $resourceGroupId;
                $resourceGroupMode->type_mode = config('constants.resource_mode.mode');
                $resourceGroupMode->value = $value;
                $resourceGroupMode->Save();
            }
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
    // Updating type's and mode's
    public function updateResourceGroupTypeModes($request, $resourceGroupId)
    {
        try {
            if ($request->has('type') && !empty($request->type)) {
                $value = config('constants.resource_types.' . $request->type);
                ResourceGroupTypeModes::updateOrCreate([
                    'resource_group_id' => $resourceGroupId,
                    'type_mode'          => config('constants.resource_mode.type'),
                ], [
                    'value'              => $value,
                ]);
            }
            if ($request->has('mode')) {
                $value = config('constants.resource_mode_type.' . $request->mode);
                ResourceGroupTypeModes::updateOrCreate([
                    'resource_group_id' => $resourceGroupId,
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

    public static function cloneResourceGroupTypeModes($originalResourceGroupAssociation, $clonedResourceGroupId)
    {
        try {
            if($originalResourceGroupAssociation){
                $cloneResourceGroupSKills = $originalResourceGroupAssociation->replicate();
                $cloneResourceGroupSKills->resource_group_id = $clonedResourceGroupId;
                $cloneResourceGroupSKills->save();
            }
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
