<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabTypeModes;

class LabTypeModesService
{
    public function labTypeModes($request, $labId)
    {
        try {
            $typeMappings = [
                'assess'  => ['type' => '0', 'value' => '0'],
                'onboard' => ['type' => '0', 'value' => '1'],
                'engage'  => ['type' => '0', 'value' => '2'],
                'grow'    => ['type' => '0', 'value' => '3'],
            ];

            LabTypeModes::where(['lab_id' => $labId , 'type_mode' => '0'])->delete();

            if ($request->has('type')) {
                foreach ($request->type as $labType) {
                    if (isset($typeMappings[$labType])) {
                        LabTypeModes::create([
                            'lab_id'        => $labId,
                            'type_mode'     => $typeMappings[$labType]['type'],
                            'value'         => $typeMappings[$labType]['value'],
                        ]);
                    }
                }
            }
            if ($request->has('mode')) {
                LabTypeModes::updateOrCreate([
                    'lab_id'        => $labId,
                    'type_mode'     => config('constants.lab_mode_type.mode'),
                ], [
                    'value'         => config('constants.lab_modes.'.$request->mode),
                ]);
            }
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    // public function updateResourceModuleTypeModes($request, $labId)
    // {
    //     try {
    //         if ($request->has('type') && !empty($request->type)) {
    //             $value = config('constants.lab_types.'.$request->type);
    //             $lab = LabTypeModes::updateOrCreate([
    //                 'lab_id' => $labId,
    //                 'type_mode'          => config('constants.lab_mode.type'),
    //             ], [
    //                 'value'              => $value,
    //             ]);
    //         }
    //         if ($request->has('mode')) {
    //             $value = config('constants.resource_mode_type.'.$request->mode);
    //             $lab = LabTypeModes::updateOrCreate([
    //                 'lab_id' => $labId,
    //                 'type_mode'          => config('constants.lab_mode.mode'),
    //             ], [
    //                 'value'              => $value,
    //             ]);
    //         }

    //         return true;
    //     } catch (\Exception $e) {
    //         UtilityHelper::logError($e);

    //         return false;
    //     }
    // }

    public static function getResourceModuleBasedOnType($type)
    {
        try {
            // Type 0 belongs to type and type 1 belongs to mode
            return LabTypeModes::where(['type_mode'=>config('constants.lab_mode.type'), 'value'=>config('constants.lab_types.'.$type)])->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function cloneResourceModuleTypeModes($originalResourceModuleAssociation, $clonedResourceModuleId)
    {
        try {
            if ($originalResourceModuleAssociation) {
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

    // public static function getResourceModuleType($labId)
    // {
    //     try {
    //         return LabTypeModes::where([
    //             'type_mode'          => config('constants.lab_mode.type'),
    //             'lab_id' => $labId])->first();
    //     } catch (\Exception $e) {
    //         UtilityHelper::logError($e);

    //         return false;
    //     }
    // }

    // public static function getResourceModuleMode($labId)
    // {
    //     try {
    //         return LabTypeModes::where([
    //             'type_mode'          => config('constants.lab_mode.mode'),
    //             'lab_id' => $labId])->first();
    //     } catch (\Exception $e) {
    //         UtilityHelper::logError($e);

    //         return false;
    //     }
    // }
}
