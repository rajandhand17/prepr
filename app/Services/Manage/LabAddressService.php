<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
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

    public function updateLabAddress($request, $lab_id)
    {
        try {
            // Attempt to find the lab address or create a new instance if it doesn't exist
            $labaddress = LabAddress::firstOrNew(['lab_id' => $lab_id]);

            // Update the fields only if they are present in the request
            $labaddress->latitude = $request->get('latitude', $labaddress->latitude);
            $labaddress->longitude = $request->get('longitude', $labaddress->longitude);
            $labaddress->address = $request->get('address', $labaddress->address);
            $labaddress->city = $request->get('city', $labaddress->city);
            $labaddress->country = $request->get('country', $labaddress->country);

            // Save the updated or newly created lab address
            $labaddress->save();

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function cloneLabAddress($lab, $newLabId)
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
}
