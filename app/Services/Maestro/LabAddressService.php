<?php

namespace App\Services\Maestro;

use App\Models\LabAddress;

class LabAddressService
{
    public static function createLabAddress($lab, $newLabId)
    {
        $labAddressExitData = LabAddress::where('lab_id', $lab->id)->first();
        if ($labAddressExitData) {
            $labaddress = new LabAddress();
            $labaddress->lab_id = $newLabId->id;
            $labaddress->latitude = $labAddressExitData->latitude;
            $labaddress->longitude = $labAddressExitData->longitude;
            $labaddress->address = $labAddressExitData->address;
            $labaddress->city = $labAddressExitData->city;
            $labaddress->country = $labAddressExitData->country;
            $labaddress->save();
        }

        return true;
    }

    public function updateLabAddress($request, $lab_id)
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
            $deleteLabAddress = LabAddress::where('lab_id', $lab_id)->delete();

            if (!$deleteLabAddress) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
