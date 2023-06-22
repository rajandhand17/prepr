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
        'full_address',
        'address_1',
        'address_2',
        'city',
        'state',
        'country',
        'zip_code',
    ];
}
