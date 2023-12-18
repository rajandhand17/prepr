<?php

namespace App\Services\Manage;

use App\Models\LabAddress;
use App\Models\LabTemplate;
use App\Models\LabTemplateAddress;
use App\Models\TemplateLabAddress;

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

    public static function createTemplateLabAddress($createLab,$lab){
        try {
            $labTemplate=LabAddress::where("lab_id",$lab->id)->get();
            foreach($labTemplate as $template){
                $labTemplateAddress = new LabTemplateAddress();
                $labTemplateAddress->template_lab_id = $createLab->id;
                $labTemplateAddress->latitude        = $template->latitude;
                $labTemplateAddress->longitude       = $template->longitude;
                $labTemplateAddress->address         = $template->address;
                $labTemplateAddress->city            = $template->city;
                $labTemplateAddress->country         = $template->country;
                $labTemplateAddress->save();
            }
            return true;
        }catch (\Exception $e) {
            return false;
        }
    }
}
