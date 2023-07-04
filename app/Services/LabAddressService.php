<?php

namespace App\Services;

use App\Models\LabAddress;

class LabAddressService
{
    public function createLabAddress($request, $lab)
    {
        try {
            $labaddress = new LabAddress();
            $labaddress->lab_id = $lab->id;
            $labaddress->latitude = $request->latitude;
            $labaddress->longitude = $request->longitude;
            $labaddress->address = $request->address;
            $labaddress->city = $request->city;
            $labaddress->country = $request->country;
            $labaddress->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
