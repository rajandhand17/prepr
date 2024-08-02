<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\OrganizationAddress;
use Exception;

class OrganizationAddressService
{
    public static function updateOrganizationAddress($request, $org_id)
    {
        try {
            if ($request->address) {
                OrganizationAddress::where('organization_id', $org_id)->delete();
                $organization_address = new OrganizationAddress();
                $organization_address->organization_id = $org_id;
                $organization_address->full_address = $request->address;
                $organization_address->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getOrganizationAddressById($orgId)
    {
        try {
            if ($orgId) {
                $orgAdress = OrganizationAddress::where('organization_id', $orgId)->get();

                return $orgAdress;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteOrganizationAddress($id)
    {
        try {
            $deleteOrgAddress = OrganizationAddress::where('organization_id', $id)->delete();

            if (!$deleteOrgAddress) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function createOrganizationAddress($request, $orgId)
    {
        try {
            $org_address = [
                'organization_id' => $orgId,
                'latitude'        => $request->latitude,
                'longitude'       => $request->longitude,
                'full_address'    => $request->address,
                'city'            => $request->city2,

            ];
            $orgAdreess = OrganizationAddress::create($org_address);

            return  true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
