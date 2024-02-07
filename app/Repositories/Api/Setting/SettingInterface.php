<?php

namespace App\Repositories\Api\Setting;

interface SettingInterface
{
    public function updateUserAccount($request, $userId);

    public function changePassword($request, $userId);

    public function updatePrivacy($request, $userDetails);

    public function updateNotification($request, $userDetails);

    public function removeProfileImage($checkUserExistsOrNot);

    public function deactivateUserAccount($action, $userId);
}
