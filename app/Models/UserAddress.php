<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id', 'latitude', 'longitude', 'address', 'city', 'state', 'country', 'zip_code',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
