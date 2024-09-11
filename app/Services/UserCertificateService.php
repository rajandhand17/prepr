<?php

namespace App\Services;

use App\Helpers\MixpanelHelper;
use App\Helpers\UtilityHelper;
use App\Models\UserCertificate;

class UserCertificateService
{
    public function addCertificate($request)
    {
        try {
            $deleteExisitingCertificate = UserCertificate::where('user_id', auth()->user()->id)->delete();
            $inputs = $request->all();
            $allCertificates = [];
            foreach ($inputs['company'] as $key => $value) {
                $certificate = UserCertificate::create([
                    'user_id'    => auth()->user()->id,
                    'company'    => $value,
                    'name'       => $inputs['name'][$key],
                    'start_date' => $inputs['start_date'][$key],
                    'end_date'   => $inputs['end_date'][$key],
                    'description'=> $inputs['description'][$key],
                ]);
                $allCertificates[] = $certificate;
            }
            $profile_data = [
                'type' => 'certificate',
                'info' => $inputs,
            ];
            $mixpenal=MixpanelHelper::mixpanel_tracking(config('mixpanel.update_profile'), $profile_data, auth()->user(), $request->ip());
            return $allCertificates;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteUserCertificate($id)
    {
        try {
            return UserCertificate::where('id', $id)->delete();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkUserCertificate($id)
    {
        try {
            return UserCertificate::where('id', $id)->first();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
