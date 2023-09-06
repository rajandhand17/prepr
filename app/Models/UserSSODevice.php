<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Exception;

class UserSSODevice extends Model
{
    use SoftDeletes;

    protected $table = 'user_sso_devices';

    protected $fillable = [
        'user_id', 'sso_type', 'sub', 'device_token'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public static function create()
    {
        try {
            dd("in Model");
            //code...
        } catch (Exception $e) {
            return false;
        }
    }
}
