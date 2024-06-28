<?php

namespace App\Services\Maestro\SkillsActivityAward;

use App\Models\SkillsActivityAward;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;

class SkillsActivityAwardService
{
    public static function updateSkillsActivityAwardById($id, $request)
    {  
        try {
            $award = SkillsActivityAward::find($id);
            $input = $request->all();
            $validation_array = [
                'name' => 'required|max:25',
                'skill' => 'required',
                'image' => 'mimes:jpg,png,jpeg',
                'points' => 'required|integer|min:0',
            ];
            // dd($request->skill);
            $image = '';
            if ($request->image) {
                $image = $request->image->store('uploads/trophy', 's3');
            }
            $validation = Validator::make($request->all(), $validation_array);
            if ($validation->fails()) {
                return Redirect::back()->withErrors($validation)->withInput();
            }
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('skills_activity_awards', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            if ($image !== '') {
                $insertArray['image'] = $image;
            }

            if (!empty($insertArray)) {
                $award->update($insertArray);
                return true;
            }
        } catch (Exception $e) {
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
            return false;
        }
    }
    public static function createSkillsActivityAward($request)
    {
        try {
            $input = $request->all();
            $validation_array = [
                'name' => 'required|max:25',
                'skill' => 'required',
                'image' => 'required|mimes:jpg,png,jpeg',
                'points' => 'required|integer|min:0',
            ];

            $image = '';
            if ($request->image) {
                $image = $request->image->store('uploads/trophy', 's3');
            }
            $validation = Validator::make($request->all(), $validation_array);
            if ($validation->fails()) {
                return Redirect::back()->withErrors($validation)->withInput();
            }
            $insertArray = [];
            foreach ($input as $key => $value) {
                if (Schema::hasColumn('skills_activity_awards', $key)) {
                    $insertArray[$key] = $value;
                }
            }
            $insertArray['image'] = $image;
            // $insertArray['skill'] = $request->skill
            if ($insertArray !== null) {
                $award = SkillsActivityAward::create($insertArray);
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }
    
    public static function getSkillsActivityAward()
    {
        try {
            return SkillsActivityAward::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }

}
