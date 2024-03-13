<?php

namespace App\Services\Manage;

use App\Models\OrganizationAddress;
use DB;

class OrganizationAddressService
{
    public static function createOrganizationAddress($request, $organization_id)
    {
        try {
            if (isset($request->organization_address) && !empty($request->organization_address)) {
                DB::beginTransaction();
                foreach ($request->organization_address as $data) {
                    $organization_address = new OrganizationAddress();
                    $organization_address->organization_id = $organization_id;
                    $organization_address->latitude = isset($data['latitude']) ? $data['latitude'] : null;
                    $organization_address->longitude = isset($data['longitude']) ? $data['longitude'] : null;
                    $organization_address->full_address = $data['address_1'].', '.$data['address_2'];
                    $organization_address->address_1 = $data['address_1'];
                    $organization_address->address_2 = $data['address_2'];
                    $organization_address->city = $data['city'];
                    $organization_address->state = $data['state'];
                    $organization_address->country = $data['country'];
                    $organization_address->zip_code = $data['zip_code'];
                    $organization_address->save();
                }
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch (\Exception $e) {
            DB::rollback();

            return false;
        }
    }

    public static function updatesOrganizationAddress($request, $organization_id)
    {
        try {
            DB::beginTransaction();
            if (isset($request->organization_address) && !empty($request->organization_address)) {
                OrganizationAddress::where('organization_id', $organization_id)->delete();
                foreach ($request->organization_address as $data) {
                    $organization_address = new OrganizationAddress();
                    $organization_address->organization_id = $organization_id;
                    $organization_address->latitude = isset($data['latitude']) ? $data['latitude'] : null;
                    $organization_address->longitude = isset($data['longitude']) ? $data['longitude'] : null;
                    $organization_address->full_address = $data['address_1'].', '.$data['address_2'];
                    $organization_address->address_1 = $data['address_1'];
                    $organization_address->address_2 = $data['address_2'];
                    $organization_address->city = $data['city'];
                    $organization_address->state = $data['state'];
                    $organization_address->country = $data['country'];
                    $organization_address->zip_code = $data['zip_code'];
                    $organization_address->save();
                }
                DB::commit();

                return true;
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public static function deleteOrganizationAddress($organizationId)
    {
        try {
            $organization = OrganizationAddress::where('organization_id', $organizationId)->first();
            if ($organization) {
                $organization = OrganizationAddress::where('organization_id', $organizationId)->delete();
                if ($organization) {
                    return true;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
