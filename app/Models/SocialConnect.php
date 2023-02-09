<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialConnect extends Model
{
    use HasFactory;


    use SoftDeletes;

    protected $table = 'social_connect';

    protected $fillable = [
        'name',
        'logo',
        'integration_status'
    ];


    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

}
