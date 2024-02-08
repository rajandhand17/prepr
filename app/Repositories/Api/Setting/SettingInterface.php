<?php

namespace App\Repositories\Api\Setting;

interface SettingInterface
{
    public function updateUserAccount($request);

    public function changePassword($request);

    public function updatePrivacy($request);

    public function updateNotification($request);

    public function removeProfileImage();

    public function deactivateUserAccount();
}
