<?php

namespace App\Services\Maestro\Setting;

use App\Models\Setting;
use Exception;

class SettingService
{
    public static function getSettings()
    {
        try {
            return Setting::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getSettingById($id)
    {
        try {
            $setting = Setting::find($id);
            if ($setting != null) {
                return $setting;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateSettingById($id, $request)
    {
        try {
            $data = $request->post();
            $setting = Setting::findOrFail($id);
            if (!empty($setting)) {
                $value = '';
                if ($setting->module_type == '5') {
                    $value = $request->file('value')->store('uploads/setting', 's3');
                } else {
                    $value = $request->value;
                }
                $setting->label = $request->label;
                $setting->value = $value;
                if ($setting->save()) {
                    return true;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
