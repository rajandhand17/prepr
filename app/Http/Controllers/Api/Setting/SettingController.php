<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AddAccountRequest;
use App\Repositories\Api\Setting\SettingRepository;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private $settingRepository;

    public function __construct(SettingRepository $settingRepository){
        $this->settingRepository=$settingRepository;
    }

    public function updateAccount(AddAccountRequest $request){
        try {
            $account = $this->settingRepository->updataUserAccount($request);
            if($account){
                return $this->sendResponse([],__('responses.update_user_account_successful'));
            }
             return $this->sendError(__('responses.update_user_account_failed'));
        }catch(\Exception $e){
            return false;
        }
    }
}
