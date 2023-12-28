<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use phpseclib3\Exception\FileNotFoundException;

class UserAddressService
{
    public function addUserAddress($request){
        try{
            $userAddress=UserAddress::updateOrCreate(
                [
                    'user_id' => auth()->user()->id,
                ],[
                   'address' => $request->address,
                   'city' => $request->city,
                   'state'=>$request->state,
                   'country' => $request->country,
            ]);
            return true;
        }catch(\Exception $e){
            return false;
        }
    }
}
