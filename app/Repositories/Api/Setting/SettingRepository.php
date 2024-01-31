<?php

namespace App\Repositories\Api\Setting;

use App\Services\SettingService;
use DB;

class SettingRepository implements SettingInterface
{
    private $settingService;
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function updataUserAccount($request){
        try {
            $settingService=$this->settingService->updataUserAccount($request);
            if($settingService){

            }
        }catch(\Exception $e){
            return false;
        }
    }
}
