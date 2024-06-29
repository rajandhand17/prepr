<?php

namespace App\Repositories\Api\Setting;

use App\Helpers\UtilityHelper;
use App\Services\UserService;
use App\Services\UserSettingService;

class SettingRepository implements SettingInterface
{
    private $userSettingService;

    private $userService;

    public function __construct(UserSettingService $userSettingService, UserService $userService)
    {
        $this->userSettingService = $userSettingService;
        $this->userService = $userService;
    }

    public function removeProfileImage()
    {
        try {
            return $this->userService->removeProfileImage();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function updateUserAccount($request)
    {
        try {
            return $this->userService->updateUserAccount($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function changePassword($request)
    {
        try {
            return $this->userService->changePassword($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function deactivateUserAccount()
    {
        try {
            return $this->userService->deactivateUserAccount();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function updatePrivacy($request)
    {
        try {
            return $this->userSettingService->updatePrivacy($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function updateNotification($request)
    {
        try {
            return $this->userSettingService->updateNotification($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
