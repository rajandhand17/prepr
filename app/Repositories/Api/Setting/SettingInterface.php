<?php

namespace App\Repositories\Api\Setting;

interface SettingInterface
{
    public function updataUserAccount($request);

    public function changePassword($request);

    public function updatePrivacy($request);

    public function updateNotification($request);
}
