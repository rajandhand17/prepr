<?php

namespace App\Services\Maestro;

use App\Models\LabAddress;

class LabAddressService
{
    public static function createLabAddress($lab, $newLabId)
    {
        try {
            if ($lab) {
                $cloneLabAddress = $lab->replicate();
                $cloneLabAddress->lab_id = $newLabId;
                $cloneLabAddress->save();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
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
