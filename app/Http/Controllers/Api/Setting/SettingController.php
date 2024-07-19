<?php

namespace App\Http\Controllers\Api\Setting;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Http\Resources\User\UserResource;
use App\Repositories\Api\Setting\SettingRepository;
use Illuminate\Support\Facades\Hash;

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
            if (!in_array($activity, ['account', 'password', 'privacy', 'notification'])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            switch ($activity) {
                case 'account':
                    $account = $this->settingRepository->updateUserAccount($request);
                    if ($account) {
                        return $this->sendResponse(UserResource::make($account), __('responses.update_user_account_successful'));
                    }
                    break;
                case 'password':
                    if (Hash::check($request->password, auth()->user()->password)) {
                        return $this->sendError(__('responses.same_password'), 422);
                    }
                    $changePassword = $this->settingRepository->changePassword($request);
                    if ($changePassword) {
                        return $this->sendResponse(UserResource::make($changePassword), __('responses.password_change_successfully'));
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
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deactivateAccount()
    {
        try {
            $updatePrivacy = $this->settingRepository->deactivateUserAccount();
            if ($updatePrivacy) {
                return $this->sendResponse([], __('responses.account_deactivate_successfully'));
            }

            return $this->sendError(__('responses.account_deactivated_failed'), 400);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteImage()
    {
        try {
            $removeProfile = $this->settingRepository->removeProfileImage();
            if ($removeProfile) {
                return $this->sendResponse(UserResource::make($removeProfile), __('responses.remove_profile_successfully'));
            }

            return $this->sendError(__('responses.remove_profile_failed'), 400);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
