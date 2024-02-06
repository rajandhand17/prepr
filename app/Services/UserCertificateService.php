<?php

namespace App\Services;

use App\Models\UserAddress;
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
            return $allCertificates;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteUserCertificate($id)
    {
        try {
            return UserCertificate::where('id', $id)->delete();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkUserCertificate($id)
    {
        try {
            return UserCertificate::where('id', $id)->first();
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteCertificateBasedOnUserId($userId){
        try {
            $getUserCertificateId=UserCertificate::where('user_id',$userId)->pluck('id');
            if($getUserCertificateId->isNotEmpty()){
                $deleteUserCertificate=UserCertificate::whereIn('id',$getUserCertificateId)->delete();
                if(!$deleteUserCertificate){
                    return false;
                }
                return true;
            }
            return true;
        }catch(\Exception $e) {
            return false;
        }
    }
}
