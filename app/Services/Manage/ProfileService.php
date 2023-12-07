<?php

namespace App\Services\Manage;

use App\Models\User;
use App\Models\UserPersonal;
use App\Models\UserSkills;
use DB;

class ProfileService
{
    public static function getProfileBasedOnUserName($user_name)
    {
        try {
            $profile = User::where('username', $user_name)->first();
            if ($profile != null) {
                return $profile;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addPersonalDetail($request)
    {
        try {
            DB::beginTransaction();
            $createdPersonal = new UserPersonal();
            $createdPersonal->user_id = $request->user_id;
            $createdPersonal->age = $request->age;
            $createdPersonal->about = $request->about;
            $createdPersonal->purpose = $request->purpose;
            $createdPersonal->gender = $request->gender;
            $createdPersonal->date_of_birth = $request->dob;
            $createdPersonal->save();
            DB::commit();
            return $createdPersonal;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addSkills($request){
        try {
            DB::beginTransaction();
            $checkSKillsExistsOrNot=UserSkills::where(['user_id' => auth()->user()->id,'skills' => $request->skills]);
           if(!$checkSKillsExistsOrNot){
               $addSkills = new UserSkills();
               $addSkills->user_id = $request->user_id;
               $addSkills->skills = $request->skills;
               $addSkills->save();
            }
           $addSkills->skills=$request->skills;
           $addSkills->save();
            DB::commit();
        }catch(\Exception $e){
            return false;
        }
    }
}
