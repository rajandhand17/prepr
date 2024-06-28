<?php

namespace App\Traits\Maestro\Setting;

use App\Services\Maestro\Setting\SettingService;
use Exception;

trait SettingTrait
{
    private function getSettings()
    {
        try {
            $settings = SettingService::getSettings();
            if ($settings) {
                return $settings;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getSettingById($id)
    {
        try {
            return SettingService::getSettingById($id);
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateSettingById($id, $request)
    {
        try {
            if (SettingService::updateSettingById($id, $request)) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
