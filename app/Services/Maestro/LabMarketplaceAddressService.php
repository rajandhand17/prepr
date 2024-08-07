<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\LabAddress;
use App\Models\LabMarketplaceAddress;
use Exception;

class LabMarketplaceAddressService
{
    public static function addLabMarketplaceAddress($labMarketplace, $labId)
    {
        try {
            $labTemplate = LabAddress::where('lab_id', $labId)->get();
            if (!empty($labTemplate)) {
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
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
