<?php
namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\OrganizationAddress;
use Illuminate\Http\Request;

class OrganizationAddressService
{
    public function createOrganizationAddress(Request $request,$profile_image_path,$cover_image_path)
    {
        try {
            $organization_address = new OrganizationAddress();
            $organization_address->organization_id = $request->organization_id;
            $organization_address->latitude = $request->has('latitude') ? $request->latitude : null;
            $organization_address->longitude = $request->has('longitude') ? $request->longitude : null;
            $organization_address->address = $request->address;
            $organization_address->city = $request->city;
            $organization_address->state = $request->state;
            $organization_address->country = $request->country;
            $organization_address->zip_code = $request->zip_code;
            if ($organization_address->save()) {
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
}
