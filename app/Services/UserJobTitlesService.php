<?php

namespace App\Services;


use App\Models\UserJobTitle;
use Illuminate\Support\Facades\Auth;

class UserJobTitlesService
{
    public static function getUsersJobs(){
        try{
            $getCurrentUsersJobs=UserJobTitle::where('user_id',auth()->user()->id)->get();
            if(!empty($getCurrentUsersJobs)){
                return $getCurrentUsersJobs;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }
}
