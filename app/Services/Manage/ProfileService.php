<?php

namespace App\Services\Manage;

use App\Models\User;
use App\Models\UserPersonal;

class ProfileService
{
    public static function getProfileBasedOnUserName($user_name)
    {
        try {
            $profile = User::where('username', $user_name)->first();
            if($profile != null){
                return $profile;
            }
            return false;
        } catch(\Exception $e) {
            return false;
        }
    }
    public function addPersonalDetail($request)
    {
        try{
            $createdPersonal=UserPersonal::where('user_id',$request->user_id)->first();

            if(!$createdPersonal!= null){
                $createdPersonal=new UserPersonal();
                $createdPersonal->age = $request->age;
                $createdPersonal->about = $request->about;
                $createdPersonal->purpose = $request->purpose;
                $createdPersonal->gender = $request->gender;
                $createdPersonal->date_of_birth = $request->dob;
                $createdPersonal->save();
            }
            $createdPersonal->age =($request->has('age')) ? $request->age : $createdPersonal->age;
            $createdPersonal->about = ($request->has('about')) ? $request->about : $createdPersonal->about;
            $createdPersonal->purpose =($request->has('purpose')) ? $request->purpose : $createdPersonal->purpose;
            $createdPersonal->gender =($request->has('gender')) ? $request->gender : $createdPersonal->gender;
            $createdPersonal->date_of_birth =($request->has('dob')) ? $request->dob : $createdPersonal->dob;
            $createdPersonal->save();
            return $createdPersonal;
        }catch(\Exception $e){
            dd($e);
            return false;
        }
    }
}
