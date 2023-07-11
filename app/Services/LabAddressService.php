<?php

namespace App\Services;

use App\Models\LabAddress;

class LabAddressService
{
    public function createLabAddress($request, $lab)
    {
        $labaddress = new LabAddress();
        $labaddress->lab_id = $lab->id;
        $labaddress->latitude = $request->latitude;
        $labaddress->longitude = $request->longitude;
        $labaddress->address = $request->address;
        $labaddress->city = $request->city;
        $labaddress->country = $request->country;
        $labaddress->save();

        return true;
    }

    public function updateLabAddress($lab_id, $request)
    {
        try {
            $labaddress = LabAddress::where('lab_id', $lab_id)->first();
            $labaddress->latitude = ($request->has('latitude')) ? $request->latitude : $labaddress->latitude;
            $labaddress->longitude = ($request->has('longitude')) ? $request->longitude : $labaddress->longitude;
            $labaddress->address = ($request->has('address')) ? $request->address : $labaddress->address;
            $labaddress->city = ($request->has('city')) ? $request->city : $labaddress->city;
            $labaddress->country = ($request->has('country')) ? $request->country : $labaddress->country;
            $labaddress->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteLabAddress($lab_id)
    {
        try {
            $deleteLabaddress = LabAddress::where('lab_id', $lab_id)->delete();
            
            if (!$deleteLabaddress) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
