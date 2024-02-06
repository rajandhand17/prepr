<?php

namespace App\Services;

use App\Models\UserAddress;

class UserAddressService
{
    public function addUserAddress($request)
    {
        try {
            $userAddress = UserAddress::updateOrCreate(
                [
                    'user_id' => auth()->user()->id,
                ],
                [
                    'address' => $request->address,
                    'city'    => $request->city,
                    'state'   => $request->state,
                    'country' => $request->country,
                ]
            );

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteUserAddressBasedOnUserId($userId)
    {
        try {
            $getUserAddressId=UserAddress::where('user_id',$userId)->pluck('id');
            if($getUserAddressId->isNotEmpty()){
                $deleteUserAddress=UserAddress::whereIn('id',$getUserAddressId)->delete();
                if(!$deleteUserAddress){
                    return false;
                }
                return true;
            }
            return true;
        }catch (\Exception $e) {
            return false;
        }
    }
}
