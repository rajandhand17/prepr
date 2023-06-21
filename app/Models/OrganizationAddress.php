<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationAddress extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'organization_addresses';

    protected $fillable = [
        'organization_id',
        'latitude',
        'longitude',
        'address',
        'city',
        'state',
        'country',
        'zip_code',
    ];

    public static function updates($request)
    {
        try {
            $organization_address_records = OrganizationAddress::where('organization_id', $request->organization_id)->get();
            $organization_address = OrganizationAddress::find($request->organization_id);
            $organization_address->latitude = $request->latitude ? $request->latitude : $organization_address_records->latitude;
            $organization_address->longitude = $request->longitude ? $request->longitude : $organization_address_records->longitude;
            $organization_address->address = $request->address ? $request->address : $organization_address_records->address;
            $organization_address->city = $request->city ? $request->city : $organization_address_records->city;
            $organization_address->state = $request->state ? $request->state : $organization_address_records->state;
            $organization_address->country = $request->country ? $request->country : $organization_address_records->country;
            $organization_address->zip_code = $request->zip_code ? $request->zip_code : $organization_address_records->zip_code;
            if ($organization_address->save()) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
