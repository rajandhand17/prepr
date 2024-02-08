<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Http\Resources\User\UserResource;
use App\Repositories\Api\Setting\SettingRepository;

class SettingController extends AppBaseController
{
    private $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function updateBasedOnActivity($activity, UpdateSettingRequest $request)
    {
        try {
            if (!in_array($activity, ['image', 'account', 'password', 'privacy', 'notification', 'deactivate'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            switch ($activity) {
                case 'image':
                    $removeProfile = $this->settingRepository->removeProfileImage();
                    if ($removeProfile) {
                        return $this->sendResponse(UserResource::make($removeProfile), __('responses.remove_profile_successfully'));
                    }
                    break;
                case 'account':
                    $account = $this->settingRepository->updateUserAccount($request);
                    if ($account) {
                        return $this->sendResponse(UserResource::make($account), __('responses.update_user_account_successful'));
                    }
                    break;
                case 'password':
                    $changePassword = $this->settingRepository->changePassword($request);
                    if ($changePassword) {
                        return $this->sendResponse(UserResource::make($changePassword), __('responses.password_change_successfully'));
                    }
                    break;
                case 'deactivate':
                    $updatePrivacy = $this->settingRepository->deactivateUserAccount();
                    if ($updatePrivacy) {
                        return $this->sendResponse([], __('responses.account_deactivate_successfully'));
                    }
                    break;
                case 'privacy':
                    $updatePrivacy = $this->settingRepository->updatePrivacy($request);
                    if ($updatePrivacy) {
                        return $this->sendResponse(UserResource::make($updatePrivacy), __('responses.update_privacy_successfully'));
                    }
                    break;
                case 'notification':
                    $updateNotification = $this->settingRepository->updateNotification($request);
                    if ($updateNotification) {
                        return $this->sendResponse(UserResource::make($updateNotification), __('responses.update_notification_successfully'));
                    }
                    break;
                default:
                    return $this->sendError(__('responses.handler_bad_request'), 400);
            }

            return $this->sendError(__('responses.send_error'), 403);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
