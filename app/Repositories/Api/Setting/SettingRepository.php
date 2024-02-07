<?php

namespace App\Repositories\Api\Setting;

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

    public function removeProfileImage($checkUserExistsOrNot)
    {
        try {
            return $this->userService->removeProfileImage($checkUserExistsOrNot);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateUserAccount($request,$userId)
    {
        try {
            return $this->userService->updataUserAccount($request,$userId);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function changePassword($request,$userId)
    {
        try {
            return $this->userService->changePassword($request,$userId);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deactivateUserAccount($action,$userId)
    {
        try {
            if (isset($action) && $action == 'deactivate') {
                return $this->userService->deactivateUserAccount($userId);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updatePrivacy($request,$userDetails)
    {
        try {
            return $this->userSettingService->updatePrivacy($request,$userDetails);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function updateNotification($request,$userDetails)
    {
        try {
            return $this->userSettingService->updateNotification($request,$userDetails);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getDetails()
    {
        try {
            return $this->userSettingService->getDetails();
        } catch(\Exception $e) {
            return false;
        }
    }
}
