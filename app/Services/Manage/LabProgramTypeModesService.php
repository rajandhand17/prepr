<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabProgramTypeModes;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class LabProgramTypeModesService
{
    public function labProgramTypeModes($request, $labProgramId)
    {
        try {
            $typeMappings = [
                'assess'  => ['type' => '0', 'value' => '0'],
                'onboard' => ['type' => '0', 'value' => '1'],
                'engage'  => ['type' => '0', 'value' => '2'],
                'grow'    => ['type' => '0', 'value' => '3'],
            ];
            LabProgramTypeModes::where(['lab_program_id' => $labProgramId , 'type_mode' => '0'])->delete();
            if ($request->has('type')) {
                foreach ($request->type as $labProgramType) {
                    if (isset($typeMappings[$labProgramType])) {
                        LabProgramTypeModes::create([
                            'lab_program_id'=> $labProgramId,
                            'type_mode'     => $typeMappings[$labProgramType]['type'],
                            'value'         => $typeMappings[$labProgramType]['value'],
                        ]);
                    }
                }
            }
            if ($request->has('mode')) {
                LabProgramTypeModes::updateOrCreate([
                    'lab_program_id'        => $labProgramId,
                    'type_mode'     => config('constants.lab_program_mode_type.mode'),
                ], [
                    'value'         => config('constants.lab_program_modes.'.$request->mode),
                ]);
            }
            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
    public static function getLabProgramType($labType)
    {
        try {
            $assessLabIds = collect([]);$onboardLabIds = collect([]); $engageLabIds = collect([]); $growLabIds = collect([]);
            if(in_array('assess',$labType)){
                $assessLabIds = collect(config('constants.lab_program_type.assess'));
            } 
            if(in_array('onboard',$labType)){
                $onboardLabIds = collect(config('constants.lab_program_type.onboard'));
            } 
            if(in_array('engage',$labType)){
                $engageLabIds = collect(config('constants.lab_program_type.engage'));
            } 
            if(in_array('grow',$labType)){
                $growLabIds = collect(config('constants.lab_program_type.grow'));
            }
            $labsCollection = new Collection;
            $labsCollection = $labsCollection->concat($assessLabIds);
            $labsCollection = $labsCollection->concat($onboardLabIds);
            $labsCollection = $labsCollection->concat($engageLabIds);
            $labsCollection = $labsCollection->concat($growLabIds);
            return LabProgramTypeModes::where(['type_mode' => '0'])->whereIn('value',$labsCollection)->pluck('lab_program_id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}