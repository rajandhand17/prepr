<?php

namespace App\Traits\Maestro\VendorManagement;

use App\Services\Maestro\VendorService;

trait VendorManagementTrait
{
    public function getAllVendorData()
    {
        try {
            $getAllVendorData = VendorService::getVendor();
            if($getAllVendorData){
                return $getAllVendorData;
            }
        }catch (\Exception $e) {
            return false;
        }
    }

    public function getVendorById($id)
    {
        try {
            $getVendor=VendorService::getVendorById($id);
            return $getVendor;
        }catch (\Exception $e){
            return false;
        }
    }

    public function updateVendorById($id,$request)
    {
        try {
            $getVendor=VendorService::updateVendorById($id,$request);
            return $getVendor;
        }catch (\Exception $e){
            return false;
        }
    }

    public function deleteVendorById($id)
    {
        try {
            $deleteVendor=VendorService::deleteVendorById($id);
            if($deleteVendor){
                return true;
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }

    public function checkVendorExists($id)
    {
        try {
            $deleteVendor=VendorService::getVendorById($id);
            if($deleteVendor){
                return $deleteVendor;
            }
        }catch (\Exception $e){
            return false;
        }
    }
}
