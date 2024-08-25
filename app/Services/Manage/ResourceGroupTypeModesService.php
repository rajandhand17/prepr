<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
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

    public static function getResourceGroupType($resourceGroupId)
    {
        try {
            return ResourceGroupTypeModes::where([
                'type_mode'         => config('constants.resource_mode.type'),
                'resource_group_id' => $resourceGroupId])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // Storing type's and mode's
    public function createUpdateResourceGroupTypeModes($request, $resourceGroupId)
    {
        try {
            $typeMappings = [
                'assess'  => ['type' => '0', 'value' => '0'],
                'onboard' => ['type' => '0', 'value' => '1'],
                'engage'  => ['type' => '0', 'value' => '2'],
                'grow'    => ['type' => '0', 'value' => '3'],
            ];

            $modeMappings = [
                'team'       => ['type' => '1', 'value' => '4'],
                'individual' => ['type' => '1', 'value' => '5'],
            ];

            // Helper function to create resource group type modes
            $createResourceGroupTypeMode = function ($mappings, $items) use ($resourceGroupId) {
                foreach ($items as $item) {
                    if (isset($mappings[$item])) {
                        ResourceGroupTypeModes::create([
                            'resource_group_id'      => $resourceGroupId,
                            'type_mode'              => $mappings[$item]['type'],
                            'value'                  => $mappings[$item]['value'],
                        ]);
                    }
                }
            };

            // Create new resource group type modes based on request types and modes
            if ($request->has('type')) {
                ResourceGroupTypeModes::where('resource_group_id', $resourceGroupId)->where('type_mode', '0')->delete();
                $createResourceGroupTypeMode($typeMappings, $request->type);
            }

            if ($request->has('mode')) {
                ResourceGroupTypeModes::where('resource_group_id', $resourceGroupId)->where('type_mode', '1')->delete();
                $createResourceGroupTypeMode($modeMappings, $request->mode);
            }

            return true;
        } catch (\Exception $e) {
            // Log the exception or handle it according to your needs
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function cloneResourceGroupTypeModes($originalResourceGroupAssociation, $clonedResourceGroupId)
    {
        try {
            $originalResourceGroupAssociation->each(function ($association) use ($clonedResourceGroupId) {
                if ($association) {
                    $cloneResourceGroupSKills = $association->replicate();
                    $cloneResourceGroupSKills->resource_group_id = $clonedResourceGroupId;
                    $cloneResourceGroupSKills->save();
                }
            });
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
