<?php

namespace App\Services\Manage;

use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserPatient;
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

    public function addUserExperience($request)
    {
        try {
            DB::beginTransaction();
            $profile = new UserExperience();
            $profile->user_id = auth()->user()->id;
            $profile->company = $request->company;
            $profile->position = $request->position;
            $profile->start_date = $request->start_date;
            $profile->end_date = $request->end_date;
            $profile->address = $request->address;
            $profile->state = $request->state;
            $profile->country = $request->country;
            $profile->description = $request->description;
            $profile->save();
            DB::commit();

            return $profile;
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

    public function addEducation($request)
    {
        try {
            DB::beginTransaction();
            $createAddEducation = new UserEducation();
            $createAddEducation->user_id = $request->user_id;
            $createAddEducation->university = $request->university;
            $createAddEducation->degree = $request->degree;
            $createAddEducation->start_date = $request->start_date;
            $createAddEducation->end_date = $request->end_date;
            $createAddEducation->address = $request->address;
            $createAddEducation->description = $request->description;
            $createAddEducation->save();
            DB::commit();

            return $createAddEducation;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addPatent($request)
    {
        try {
            DB::beginTransaction();
            $addPatent = new UserPatient();
            $addPatent->user_id = $request->user_id;
            $addPatent->title = $request->title;
            $addPatent->name = $request->name;
            $addPatent->patent_date = $request->patent_date;
            $addPatent->description = $request->description;
            $addPatent->save();
            DB::commit();

            return $addPatent;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addSkills($request){
        try {
            DB::beginTransaction();
            $checkSKillsExistsOrNot=UserSkills::where(['user_id' => auth()->user()->id,'skill' => $request->skill_id])->first();
            if($checkSKillsExistsOrNot==null){
                $addSkills = new UserSkills();
                $addSkills->user_id = auth()->user()->id;
                $addSkills->skill = $request->skill_id;
                $addSkills->save();
               DB::commit();
               return $addSkills;
            }
            $checkSKillsExistsOrNot->skill=$request->skill_id;
            $checkSKillsExistsOrNot->save();
            DB::commit();
            return $checkSKillsExistsOrNot;
        }catch(\Exception $e){
            return false;
        }
    }
}
