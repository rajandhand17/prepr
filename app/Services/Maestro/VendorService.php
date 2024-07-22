<?php

namespace App\Services\Maestro;


use App\Models\User;
use App\Models\Vendor;

class VendorService
{
    public static function getVendor()
    {
        try {
            $getAllVendor = Vendor::select();
            if($getAllVendor){
                return $getAllVendor;
            }
        }catch (\Exception $e){
            return false;
        }
    }

    public static function getVendorById($id)
    {
        try {
            $vendor=Vendor::where('id',$id)->first();
            return $vendor;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function updateVendorById($id,$request)
    {
        try {
            $getVendorById=Vendor::where('id',$id)->first();
            if ($getVendorById){
                $getVendorById->name=$request->name;
                $getVendorById->email=$request->email;
                $getVendorById->api_key=$request->api_key;
                $getVendorById->secret_key=$request->secret_key;
                $getVendorById->is_active=$request->is_active=="yes" ? 1 : 0;
                if($getVendorById->save()){
                    return true;
                }
                return false;
            }
            return false;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function deleteVendorById($id)
    {
        try {
            $vendor = Vendor::find($id);
            if (!empty($vendor)) {
                return $vendor->delete();
            }

            return false;
        }catch (\Exception $e){
            return false;
        }
    }
}
