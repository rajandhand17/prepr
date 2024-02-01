<?php

namespace App\Repositories\Api\Setting;

use App\Services\SettingService;
use App\Services\UserService;
use App\Services\UserSettingService;
use DB;

class SettingRepository implements SettingInterface
{
    private $userSettingService;

    private $userService;
    public function __construct(UserSettingService $userSettingService,UserService $userService)
    {
        $this->userSettingService = $userSettingService;
        $this->userService=$userService;
    }

    public function removeProfile(){
        try {
            return $this->userService->removeProfile();
        }catch (\Exception $e) {
            return false;
        }
    }
    public function updataUserAccount($request)
    {
        try {
           return $this->userService->updataUserAccount($request);
        }catch(\Exception $e){
            return false;
        }
    }

    public function changePassword($request)
    {
        try{
            return $this->userService->changePassword($request);
        }catch(\Exception $e){
            return false;
        }
    }

    public function updatePrivacy($request)
    {
        try{
            return $this->userSettingService->updatePrivacy($request);
        }catch(\Exception $e){
            return false;
        }
    }

    public function updateNotification($request)
    {
        try{
            return $this->userSettingService->updateNotification($request);
        }catch(\Exception $e){
            return false;
        }
    }

    public function getDetails(){
        try{
            return $this->userSettingService->getDetails();
        }catch(\Exception $e){
            return false;
        }
    }
}
