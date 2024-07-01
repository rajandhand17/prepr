<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
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
            UtilityHelper::logError($e);

            return false;
        }
    }
}
