<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationSocialActivities extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'organisation_social_activities';

    protected $fillable = [
        'id',
        'organisation_id',
        'lab_id',
        'followers',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
