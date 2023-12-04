<?php

namespace App\Services\Manage;

use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserPersonal;
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
}
