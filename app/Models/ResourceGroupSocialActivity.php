<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceGroupSocialActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_groups_social_activities';

    protected $fillable = [
        'user_id',
        'resource_group_id',
        'like_dislike',
        'share',
        'favourite',
    ];
}
