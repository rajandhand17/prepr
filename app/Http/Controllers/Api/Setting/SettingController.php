<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AddAccountRequest;
use App\Http\Requests\Setting\ChangePasswordRequest;
use App\Http\Requests\Setting\UpdateNotificationRequest;
use App\Http\Requests\Setting\UpdatePrivacyRequest;
use App\Http\Resources\Profile\ProfileResource;
use App\Http\Resources\Settings\AccountResource;
use App\Repositories\Api\Setting\SettingRepository;
use Illuminate\Http\Request;

class SettingController extends AppBaseController
{
    private $settingRepository;

    public function __construct(SettingRepository $settingRepository){
        $this->settingRepository=$settingRepository;
    }

    public function removeProfile(){
        try {
            $removeProfile=$this->settingRepository->removeProfile();
            if($removeProfile){
                return $this->sendResponse([],__('responses.remove_profile_successfully'));
            }
            return $this->sendError(__('responses.remove_profile_failed'),400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    public function updateAccount(AddAccountRequest $request){
        try {
            $account = $this->settingRepository->updataUserAccount($request);
            if($account){
                return $this->sendResponse(AccountResource::make($account), __('responses.update_user_account_successful'));
            }
            return $this->sendError(__('responses.update_user_account_failed'));
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request){
        try {
            $changePassword=$this->settingRepository->changePassword($request);
            if($changePassword){
                return $this->sendResponse(AccountResource::make($changePassword), __('responses.update_user_account_successful'));
            }
            return $this->sendError(__('responses.change_password'),400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updatePrivacy(UpdatePrivacyRequest $request){
        try {
            $updatePrivacy=$this->settingRepository->updatePrivacy($request);
            if($updatePrivacy){
                return $this->sendResponse([],__('responses.update_privacy_successfully'));
            }
            return $this->sendError(__('responses.update_privacy_failed'), 400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updateNotification(UpdateNotificationRequest $request){
        try {
            $updateNotification=$this->settingRepository->updateNotification($request);
            if($updateNotification){
                return $this->sendResponse([],__('responses.update_notification_successfully'));
            }
            return $this->sendError(__('responses.update_notification_failed'),400);

        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function getDetails(){
        try {
            $getdetails=$this->settingRepository->getDetails();
            if($getdetails){
                return $this->sendResponse($getdetails,__('responses.get_details'));
            }
            return $this->sendError(__('responses.update_notification_failed'),400);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }
}
