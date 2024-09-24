<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleTypeModes;

class ResourceModuleTypeModesService
{
    // Base on key store data
    public function createUpdateResourceModuleTypeModes($request, $resourceModuleId)
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

            // Helper function to create resource module type modes
            $createResourceModuleTypeMode = function ($mappings, $items) use ($resourceModuleId) {
                foreach ($items as $item) {
                    if (isset($mappings[$item])) {
                        ResourceModuleTypeModes::create([
                            'resource_module_id'     => $resourceModuleId,
                            'type_mode'              => $mappings[$item]['type'],
                            'value'                  => $mappings[$item]['value'],
                        ]);
                    }
                }
            };

            // Create new resource group type modes based on request types and modes
            if ($request->has('type')) {
                ResourceModuleTypeModes::where('resource_module_id', $resourceModuleId)->where('type_mode', '0')->delete();
                $createResourceModuleTypeMode($typeMappings, $request->type);
            }

            if ($request->has('mode')) {
                ResourceModuleTypeModes::where('resource_module_id', $resourceModuleId)->where('type_mode', '1')->delete();
                $createResourceModuleTypeMode($modeMappings, $request->mode);
            }

            return true;
        } catch (\Exception $e) {
            // Log the exception or handle it according to your needs
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
            return ResourceModuleTypeModes::where(['type_mode'=>config('constants.resource_mode.type'), 'value'=>config('constants.resource_types.'.$type)])->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function cloneResourceModuleTypeModes($originalResourceModuleAssociation, $clonedResourceModuleId)
    {
        try {
            // Check if the association is a module
            if ($originalResourceModuleAssociation && $originalResourceModuleAssociation->isNotEmpty()) {
                foreach ($originalResourceModuleAssociation as $originalAssociation) {
                    // Replicate each model in the module
                    $cloneResourceModuleSkills = $originalAssociation->replicate();
                    $cloneResourceModuleSkills->resource_module_id = $clonedResourceModuleId;
                    $cloneResourceModuleSkills->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleType($resourceModuleId)
    {
        try {
            return ResourceModuleTypeModes::where([
                'type_mode'          => config('constants.resource_mode.type'),
                'resource_module_id' => $resourceModuleId])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getResourceModuleMode($resourceModuleId)
    {
        try {
            return ResourceModuleTypeModes::where([
                'type_mode'          => config('constants.resource_mode.mode'),
                'resource_module_id' => $resourceModuleId])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
