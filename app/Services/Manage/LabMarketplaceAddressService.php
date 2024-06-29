<?php

namespace App\Services\Manage;

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

    public function redeemLabMarketplaceAddress($redeemLabId, $labMarketplaceId)
    {
        try {
            $labMarketplaceAddressData = LabMarketplaceAddress::where('lab_marketplace_id', $labMarketplaceId)->first();
            if ($labMarketplaceAddressData) {
                $newLabAddress = new LabAddress();
                $newLabAddress->lab_id = $redeemLabId;
                $newLabAddress->latitude = $labMarketplaceAddressData->latitude;
                $newLabAddress->longitude = $labMarketplaceAddressData->longitude;
                $newLabAddress->address = $labMarketplaceAddressData->address;
                $newLabAddress->city = $labMarketplaceAddressData->city;
                $newLabAddress->country = $labMarketplaceAddressData->country;
                $newLabAddress->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteLabMarketplaceAddress($labMarketplaceId)
    {
        try {
            $checkLabMarketplaceAddress = LabMarketplaceAddress::where('lab_marketplace_id', $labMarketplaceId)->first();
            if ($checkLabMarketplaceAddress) {
                $deleteLabMarketplaceAddress = LabMarketplaceAddress::where('lab_marketplace_id', $labMarketplaceId)->delete();
                if (!$deleteLabMarketplaceAddress) {
                    return false;
                }

                return true;
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
