<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Setting\AddAccountRequest;
use App\Http\Requests\Setting\ChangePasswordRequest;
use App\Http\Requests\Setting\UpdateNotificationRequest;
use App\Http\Requests\Setting\UpdatePrivacyRequest;
use App\Http\Resources\Settings\AccountResource;
use App\Repositories\Api\Setting\SettingRepository;

class SettingController extends AppBaseController
{
    private $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function removeProfileImage(){
        try {
            $checkUserExistsOrNot=$this->settingRepository->getUserById(auth()->user()->id);
            if(!$checkUserExistsOrNot || $checkUserExistsOrNot->is_deactivated=='1'){
                return $this->sendError(__('responses.user_not_found'));
            }
            $removeProfile=$this->settingRepository->removeProfileImage();
            if($removeProfile){
                return $this->sendResponse(AccountResource::make($removeProfile),__('responses.remove_profile_successfully'));
            }

            return $this->sendError(__('responses.remove_profile_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updateAccount(AddAccountRequest $request)
    {
        try {
            $checkUserExistsOrNot=$this->settingRepository->getUserById(auth()->user()->id);
            if(!$checkUserExistsOrNot || $checkUserExistsOrNot->is_deactivated=='1'){
                return $this->sendError(__('responses.user_not_found'));
            }
            $account = $this->settingRepository->updateUserAccount($request);
            if($account){
                return $this->sendResponse(AccountResource::make($account), __('responses.update_user_account_successful'));
            }
            return $this->sendError(__('responses.update_user_account_failed'));
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $checkUserExistsOrNot=$this->settingRepository->getUserById(auth()->user()->id);
            if(!$checkUserExistsOrNot || $checkUserExistsOrNot->is_deactivated=='1'){
                return $this->sendError(__('responses.user_not_found'));
            }
            $changePassword=$this->settingRepository->changePassword($request);
            if($changePassword){
                return $this->sendResponse(AccountResource::make($changePassword), __('responses.password_change_successfully'));
            }
            return $this->sendError(__('responses.password_change_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updatePrivacy(UpdatePrivacyRequest $request)
    {
        try {
            $checkUserExistsOrNot=$this->settingRepository->getUserById(auth()->user()->id);
            if(!$checkUserExistsOrNot || $checkUserExistsOrNot->is_deactivated=='1'){
                return $this->sendError(__('responses.user_not_found'));
            }
            $allowedActions = ['delete', 'deactivate'];
            if (isset($request->action) && in_array($request->action, $allowedActions)){
                $updatePrivacy = $this->settingRepository->deleteOrDeactivateUserAccount($request->action);
                if ($updatePrivacy) {
                    return $this->sendResponse([], __('responses.account_' . $request->action . '_successfully'));
                }
            }
            $updatePrivacy=$this->settingRepository->updatePrivacy($request);
            if($updatePrivacy){
                return $this->sendResponse(AccountResource::make($updatePrivacy),__('responses.update_privacy_successfully'));
            }

            return $this->sendError(__('responses.update_privacy_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updateNotification(UpdateNotificationRequest $request)
    {
        try {
            $checkUserExistsOrNot=$this->settingRepository->getUserById(auth()->user()->id);
            if(!$checkUserExistsOrNot || $checkUserExistsOrNot->is_deactivated=='1'){
                return $this->sendError(__('responses.user_not_found'));
            }
            $updateNotification=$this->settingRepository->updateNotification($request);
            if($updateNotification){
                return $this->sendResponse(AccountResource::make($updateNotification),__('responses.update_notification_successfully'));
            }
            return $this->sendError(__('responses.update_notification_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getDetails()
    {
        try {
            $checkUserExistsOrNot=$this->settingRepository->getUserById(auth()->user()->id);
            if(!$checkUserExistsOrNot || $checkUserExistsOrNot->is_deactivated=='1'){
                return $this->sendError(__('responses.user_not_found'));
            }
            $getdetails=$this->settingRepository->getDetails();
            if($getdetails){
                return $this->sendResponse(AccountResource::make($getdetails),__('responses.get_details'));
            }

            return $this->sendError(__('responses.update_notification_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
