<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\LabProgramTypeModes;
use Exception;
use Illuminate\Database\Eloquent\Collection;

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
            if (LabProgramTypeModes::where(['lab_program_id' => $labProgramId, 'type_mode' => '0'])->exists()) {
                LabProgramTypeModes::where(['lab_program_id' => $labProgramId, 'type_mode' => '0'])->delete();
            }
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
            if (LabProgramTypeModes::where(['lab_program_id' => $labProgramId, 'type_mode' => '1'])->exists()) {
                LabProgramTypeModes::where(['lab_program_id' => $labProgramId, 'type_mode' => '1'])->delete();
            }
            $modeMappings = [
                'team'       => ['mode' => '1', 'value' => '4'],
                'individual' => ['mode' => '1', 'value' => '5'],
            ];

            if ($request->has('mode')) {
                foreach ($request->mode as $labProgramMode) {
                    if (isset($modeMappings[$labProgramMode])) {
                        LabProgramTypeModes::create([
                            'lab_program_id'=> $labProgramId,
                            'type_mode'     => $modeMappings[$labProgramMode]['mode'],
                            'value'         => $modeMappings[$labProgramMode]['value'],
                        ]);
                    }
                }
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
            $assessLabIds = collect([]);
            $onboardLabIds = collect([]);
            $engageLabIds = collect([]);
            $growLabIds = collect([]);
            if (in_array('assess', $labType)) {
                $assessLabIds = collect(config('constants.lab_program_type.assess'));
            }
            if (in_array('onboard', $labType)) {
                $onboardLabIds = collect(config('constants.lab_program_type.onboard'));
            }
            if (in_array('engage', $labType)) {
                $engageLabIds = collect(config('constants.lab_program_type.engage'));
            }
            if (in_array('grow', $labType)) {
                $growLabIds = collect(config('constants.lab_program_type.grow'));
            }
            $labsCollection = new Collection();
            $labsCollection = $labsCollection->concat($assessLabIds);
            $labsCollection = $labsCollection->concat($onboardLabIds);
            $labsCollection = $labsCollection->concat($engageLabIds);
            $labsCollection = $labsCollection->concat($growLabIds);

            return LabProgramTypeModes::where(['type_mode' => '0'])->whereIn('value', $labsCollection)->pluck('lab_program_id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
