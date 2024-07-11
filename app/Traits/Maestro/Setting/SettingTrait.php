<?php

namespace App\Traits\Maestro\Setting;

use App\Services\Maestro\Setting\SettingService;
use Exception;

trait SettingTrait
{
    private function getSettings()
    {
        try {
            $settings=$this->settingService->getSettings();
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
            return $this->settingService->getSettingById($id);
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateSettingById($id, $request)
    {
        try {
            $updateSettingById=$this->settingService->updateSettingById($id, $request);
            if ($updateSettingById) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
