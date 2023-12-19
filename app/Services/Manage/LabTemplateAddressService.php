<?php

namespace App\Services\Manage;

use App\Models\LabAddress;
use App\Models\LabTemplateAddress;

class LabTemplateAddressService
{
    public static function createLabTemplateAddress($createLab, $lab)
    {
        try {
            $labTemplate = LabAddress::where('lab_id', $lab->id)->get();
            foreach ($labTemplate as $template) {
                $labTemplateAddress = new LabTemplateAddress();
                $labTemplateAddress->template_lab_id = $createLab->id;
                $labTemplateAddress->latitude = $template->latitude;
                $labTemplateAddress->longitude = $template->longitude;
                $labTemplateAddress->address = $template->address;
                $labTemplateAddress->city = $template->city;
                $labTemplateAddress->country = $template->country;
                $labTemplateAddress->save();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
