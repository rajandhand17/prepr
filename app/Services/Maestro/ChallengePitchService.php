<?php

namespace App\Services\Maestro;

use App\Models\ChallengePitch;
use App\Helpers\UtilityHelper;
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
            $pitchSectionArray = [];
            $pitchData = array_map(null, $request->pitch['name']['en'], $request->pitch['description']['fr-CA']);
            foreach ($pitchData as $pitch) {
                $pitchSection['title'] = $pitch[0];
                $pitchSection['description'] = $pitch[1];
                $pitchSection['fr_CA_title'] = $pitch[0];
                $pitchSection['fr_CA_description'] = $pitch[1];
                $pitchSection['template_id'] = $pitchTemplate->id;
                $pitchSectionArray[] = $pitchSection;
            }
            ChallengePitch::insert($pitchSectionArray);

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
