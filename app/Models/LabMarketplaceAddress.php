<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabMarketplaceAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_marketplace_address';

    protected $fillable = [
        'lab_marketplace_id',
        'latitude',
        'longitude',
        'address',
        'city',
        'country',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
