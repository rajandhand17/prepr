<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectSocialActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_social_activities';
    protected $fillable = [
        'user_id',
        'project_id',
        'vote',
        'like_dislike',
        'share',
        'favourite',
    ];
}
