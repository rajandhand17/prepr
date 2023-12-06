<?php

namespace App\Services\Manage;

use App\Models\User;
use App\Models\UserPatient;
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

    public function addPatent($request){
        try {
            DB::beginTransaction();
            $addPatient = new UserPatient();
            $addPatient->user_id=$request->user_id;
            $addPatient->title=$request->title;
            $addPatient->name=$request->name;
            $addPatient->patient_date=$request->patient_date;
            $addPatient->description=$request->description;
            $addPatient->save();
            DB::commit();
        }catch(\Exception $e){
            return false;
        }
    }
}
