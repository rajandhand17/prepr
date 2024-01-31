<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class SettingService
{
    public function updataUserAccount($request){
        try {
            $user=new User();
            $user->first_name=$request->first_name;
            $user->last_name=$request->last_name;
            $user->username=$request->username;
            $user->email=$request->email;
            $user->phone_number=$request->phone_number;
            $user->preferred_language=$request->preferred_language;
            $user->two_factor_verification=$request->two_factor_verification;
            if($user->save()){
                return true;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }
}
