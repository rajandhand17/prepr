<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use phpseclib3\File\ASN1\Maps\OrganizationName;

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


    static public function create($id, $latitude,$longitude,$address,$city,$state,$country,$zipcode)
    {      
        try {
                $organization_address=new OrganizationAddress();
                $organization_address->organization_id=$id;
                $organization_address->latitude=$latitude;
                $organization_address->longitude=$longitude;
                $organization_address->address=$address;
                $organization_address->city=$city;
                $organization_address->state=$state;
                $organization_address->country=$country;
                $organization_address->zip_code=$zipcode;
                if($organization_address->save()){
                DB::commit();
                return true;
                }
                DB::rollback();
                return false;
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            return false;
        }
        
    }

    static public function updates($organization_id, $latitude, $longitude, $address, $city, $state, $country, $zipcode)
    {  
        try{  
            $organization_address_records=OrganizationAddress::where("organization_id",$organization_id)->get();
            $organization_address=OrganizationAddress::find($organization_id);
            $organization_address->latitude=$latitude?$latitude:$organization_address_records->latitude;
            $organization_address->longitude=$longitude?$longitude:$organization_address_records->longitude;
            $organization_address->address=$address?$address:$organization_address_records->address;
            $organization_address->city=$city?$city:$organization_address_records->city;
            $organization_address->state=$state?$state:$organization_address_records->state;
            $organization_address->country=$country?$country:$organization_address_records->country;
            $organization_address->zip_code=$zipcode?$zipcode:$organization_address_records->zipcode;
            if($organization_address->save()){
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
