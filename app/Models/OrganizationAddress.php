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
}
