<?php

namespace App\Services;

use App\Models\OrganizationAddress;
use Illuminate\Http\Request;
use DB;
class OrganizationAddressService
{
    public function createOrganizationAddress($request, $profile_image_path, $cover_image_path)
    {
        try {
            foreach ($request['organization_address'] as $data) {
                $organization_address = new OrganizationAddress();
                $organization_address->organization_id = $request['organization_id'];
                $organization_address->latitude = isset($data['latitude']) ? $data['latitude'] : null;
                $organization_address->longitude = isset($data['longitude']) ? $data['longitude'] : null;
                $organization_address->address = $data['address'];
                $organization_address->city = $data['city'];
                $organization_address->state = $data['state'];
                $organization_address->country = $data['country'];
                $organization_address->zip_code = $data['zip_code'];
                if ($organization_address->save()) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return $e;
           return false;
        }
    }

    public function updates($request,$organization_id){
        try {
            $organization_address_records = OrganizationAddress::where('organization_id', $organization_id)->first();
            foreach($request as $request){
            $organization_address = OrganizationAddress::find($organization_address_records->id);
            $organization_address->latitude = isset($request['latitude']) ? $request['latitude'] : $organization_address_records->latitude;
            $organization_address->longitude = isset($request['longitude']) ? $request['longitude'] : $organization_address_records->longitude;
            $organization_address->address = isset($request['address']) ? $request['address'] : $organization_address_records->address;
            $organization_address->city = isset($request['city']) ? $request['city'] : $organization_address_records->city;
            $organization_address->state = isset($request['state']) ? $request['state'] : $organization_address_records->state;
            $organization_address->country = isset($request['country']) ? $request['country'] : $organization_address_records->country;
            $organization_address->zip_code = isset($request['zip_code']) ? $request['zip_code'] : $organization_address_records->zip_code;
            if ($organization_address->save()) {
                return true;
            }
          }
            return false;
        } catch (\Exception $e){
            return $e;
            return false;
        }
    }
}
