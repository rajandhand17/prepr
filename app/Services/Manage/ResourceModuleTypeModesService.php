<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceModuleTypeModes;

class ResourceModuleTypeModesService
{
    const TYPE = 'type';
    const VALUE = 'value';
    // Defining all values in single array
    private $mappings = [
        'assess'     => [self::TYPE => '0', self::VALUE => '0'],
        'onboard'    => [self::TYPE => '0', self::VALUE => '1'],
        'engage'     => [self::TYPE => '0', self::VALUE => '2'],
        'grow'       => [self::TYPE => '0', self::VALUE => '3'],
        'team'       => [self::TYPE => '1', self::VALUE => '4'],
        'individual' => [self::TYPE => '1', self::VALUE => '5'],
    ];

    // Base on key store data
    public function createResourceModuleTypeModes($request, $resourceModuleId)
    {
        try {
            if ($request->has('type')) {
                $value = config('constants.resource_types.' . $request->type);
                $resourceModule = new ResourceModuleTypeModes();
                $resourceModule->resource_module_id = $resourceModuleId;
                $resourceModule->type_mode = config('constants.resource_mode.type');
                $resourceModule->value = $value;
                $resourceModule->Save();
            }
            if ($request->has('mode')) {
                $value = config('constants.resource_mode_type.' . $request->mode);
                $resourceModule = new ResourceModuleTypeModes();
                $resourceModule->resource_module_id = $resourceModuleId;
                $resourceModule->type_mode = config('constants.resource_mode.mode');
                $resourceModule->value = $value;
                $resourceModule->Save();
            }
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function updateResourceModuleTypeModes($request, $resourceModuleId)
    {
        try {
            if ($request->has('type') && !empty($request->type)) {
                $value = config('constants.resource_types.' . $request->type);
                $resourceModule=ResourceModuleTypeModes::updateOrCreate([
                    'resource_module_id' => $resourceModuleId,
                    'type_mode'          => config('constants.resource_mode.type'),
                ], [
                    'value'              => $value,
                ]);
            }
            if ($request->has('mode')) {
                $value = config('constants.resource_mode_type.' . $request->mode);
                $resourceModule=ResourceModuleTypeModes::updateOrCreate([
                    'resource_module_id' => $resourceModuleId,
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
            if($originalResourceModuleAssociation){
                $cloneResourceModuleSKills = $originalResourceModuleAssociation->replicate();
                $cloneResourceModuleSKills->resource_module_id = $clonedResourceModuleId;
                $cloneResourceModuleSKills->save();
            }
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
