<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\PitchTemplate;

class PitchTemplateService
{
    public function getPitchTemplates($language = 'en', $search = null)
    {
        try {
            $pitch_temple_list = PitchTemplate::select('id', 'title');
            //Search Pitch templete based on user input
            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $pitch_temple_list = $pitch_temple_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $pitch_temple_list = $pitch_temple_list->take(config('site-settings.dropdown_listing_limit'))->get();

            //check if there are any results
            if (!$pitch_temple_list->isEmpty()) {
                return $pitch_temple_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPitchTemplatesBasedOnId($id)
    {
        try {
            $pitch_temple = PitchTemplate::select('id', 'title')->where('id', $id)->get();
            if ($pitch_temple) {
                return $pitch_temple;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addPitchAndTaskTemplate($request)
    {
        try {
            $addPitchAndTaskTemplate = new PitchTemplate();
            $addPitchAndTaskTemplate->title = $request->template_title;
            $addPitchAndTaskTemplate->save();
            if (!$addPitchAndTaskTemplate) {
                return false;
            }

            return $addPitchAndTaskTemplate;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
