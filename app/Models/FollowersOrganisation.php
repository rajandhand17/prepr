<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowersOrganisation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'followers_organisation';

    protected $fillable = [
        'id',
        'organisation_id',
        'user_id',
        'followers',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
