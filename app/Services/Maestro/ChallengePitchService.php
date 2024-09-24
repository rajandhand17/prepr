<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePitch;
use Exception;

class ChallengePitchService
{
    public static function getChallengePitchById($id)
    {
        try {
            return ChallengePitch::where('template_id', $id)->get();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function saveChallengePitch($request, $pitchTemplate)
    {
        try {
            $mergedPitchData = [];
            $pitchDataEn = array_map(null, $request->pitch['name']['en'], $request->pitch['description']['en']);
            $pitchDataFr = array_map(null, $request->pitch['name']['fr-CA'], $request->pitch['description']['fr-CA']);
            if (!empty($pitchDataEn)) {
                foreach ($pitchDataEn as $index => $enData) {
                    $mergedPitchData[] = [
                        'title'             => $enData[0],
                        'description'       => $enData[1],
                        'fr_CA_title'       => $pitchDataFr[$index][0],
                        'fr_CA_description' => $pitchDataFr[$index][1],
                        'template_id'       => $pitchTemplate->id,
                    ];
                }
            }
            if (!empty($mergedPitchData)) {
                ChallengePitch::insert($mergedPitchData);
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
