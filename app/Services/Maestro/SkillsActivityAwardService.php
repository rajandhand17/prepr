<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\SkillsActivityAward;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class SkillsActivityAwardService
{
    public static function updateSkillsActivityAwardById($id, $request)
    {
        try {
            $award = SkillsActivityAward::find($id);
            $input = $request->all();
          
            $image = '';
            if ($request->image) {
                $image = FileUploadHelper::uploadImageToS3($request->image, 'skill_activity_award');
            }
           
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('skills_activity_awards', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            if ($request->skill) {
                $skills = (int) $request->skill[0];
                $insertArray['skill'] = $skills;
            }
            if ($image !== '') {
                $insertArray['image'] = $image;
            }
            if (!empty($insertArray)) {
                $award->update($insertArray);

                return true;
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteSkillsActivityAward($id)
    {
        try {
            $SkillsActivityAward = SkillsActivityAward::find($id);

            if ($SkillsActivityAward) {
                return $SkillsActivityAward->delete();
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createSkillsActivityAward($request)
    {
        try {
            $input = $request->all();
           
            $image = '';
            if ($request->image) {
                $image = FileUploadHelper::uploadImageToS3($request->image, 'skill_activity_award');
            }
            
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('skills_activity_awards', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            // Check if the array is not empty and get the first element as an integer
            $skills = !empty($request->skill) ? (int) $request->skill[0] : null;
            $insertArray['skill'] = $skills;
            $insertArray['image'] = $image;

            if ($insertArray !== null) {
                $award = SkillsActivityAward::create($insertArray);

                return true;
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getSkillsActivityAward()
    {
        try {
            return SkillsActivityAward::orderBy('id', 'desc');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
