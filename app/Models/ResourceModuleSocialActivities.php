<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceModuleSocialActivities extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $tableName = 'resource_module_social_activities';

    protected $fillable = [
        'user_id',
        'resource_module_id',
        'like_dislike',
        'share',
        'favourite',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
