<?php

namespace App\Services\Manage;

use App\Models\LabAddress;
use App\Models\LabMarketplaceAddress;
use App\Models\LabTemplateAddress;

class LabMarketplaceAddressService
{
    public static function createLabMarketplaceAddress($labMarketplace, $labId)
    {
        try {
            $labTemplate = LabAddress::where('lab_id', $labId)->get();
            foreach ($labTemplate as $template) {
                $labTemplateAddress = new LabMarketplaceAddress();
                $labTemplateAddress->lab_marketplace_id = $labMarketplace;
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

    public static function deleteLabMarketplaceAddress($labMarketplaceId){
        try {
            $checkLabMarketplaceAddress =LabMarketplaceAddress::where('lab_marketplace_id',$labMarketplaceId)->first();
            if($checkLabMarketplaceAddress){
                $deleteLabMarketplaceAddress = LabMarketplaceAddress::where('lab_marketplace_id',$labMarketplaceId)->delete();
                if(!$deleteLabMarketplaceAddress){
                    return false;
                }
                return true;
            }
        }catch (\Exception $e) {
            return false;
        }
    }
}
