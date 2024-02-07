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

    public function getUserById($id)
    {
        try {
            return $this->userService->getUserById($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function removeProfileImage()
    {
        try {
            return $this->userService->removeProfileImage();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateUserAccount($request)
    {
        try {
            return $this->userService->updataUserAccount($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function changePassword($request)
    {
        try {
            return $this->userService->changePassword($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deleteOrDeactivateUserAccount($action)
    {
        try {
            if (isset($action) && $action == 'deactivate') {
                return $this->userService->deactivateUserAccount();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updatePrivacy($request)
    {
        try {
            return $this->userSettingService->updatePrivacy($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function updateNotification($request)
    {
        try {
            return $this->userSettingService->updateNotification($request);
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
