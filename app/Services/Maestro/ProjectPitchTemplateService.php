<?php

namespace App\Services\Maestro;

use App\Helpers\Maestro\UtilityHelper;
use App\Models\PitchTemplate;
use Exception;

class ProjectPitchTemplateService
{
    public static function findPitchTemplate($id)
    {
        try {
            return PitchTemplate::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getPitchTemplate()
    {
        try {
            return PitchTemplate::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdatePitchTemplate($request, $id, $moduleMode)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            if ($moduleMode == 'edit') {
                $pitchTemplate = PitchTemplate::findOrFail($id);
            } else {
                $pitchTemplate = new PitchTemplate();
            }
            foreach ($languages as $key => $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $pitchTemplate->$columName = $request->$columName;
            }
            if ($pitchTemplate->save()) {
                return $pitchTemplate;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deletePitchTemplate($pitchTemplate)
    {
        try {
            return $pitchTemplate->delete();
        } catch (Exception $e) {
            return false;
        }
    }
}
