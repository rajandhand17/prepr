<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabTypeModes;
use Illuminate\Database\Eloquent\Collection;
use Exception;

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
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
    public static function getLabType($labType)
    {
        try {
            $assessLabIds = collect([]);$onboardLabIds = collect([]); $engageLabIds = collect([]); $growLabIds = collect([]);
            if(in_array('assess',$labType)){
                $assessLabIds = collect(config('constants.lab_type.assess'));
            } 
            if(in_array('onboard',$labType)){
                $onboardLabIds = collect(config('constants.lab_type.onboard'));
            } 
            if(in_array('engage',$labType)){
                $engageLabIds = collect(config('constants.lab_type.engage'));
            } 
            if(in_array('grow',$labType)){
                $growLabIds = collect(config('constants.lab_type.grow'));
            }
            $labsCollection = new Collection;
            $labsCollection = $labsCollection->concat($assessLabIds);
            $labsCollection = $labsCollection->concat($onboardLabIds);
            $labsCollection = $labsCollection->concat($engageLabIds);
            $labsCollection = $labsCollection->concat($growLabIds);
            return LabTypeModes::where(['type_mode' => '0'])->whereIn('value',$labsCollection)->pluck('lab_id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
