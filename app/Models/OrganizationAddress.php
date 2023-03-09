<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
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
}
