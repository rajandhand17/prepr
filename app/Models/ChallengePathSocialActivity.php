<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengePathSocialActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_path_social_activities';
    protected $fillable = [
        'user_id',
        'challenge_path_id',
        'like_dislike',
        'share',
        'favourite',
    ];
}
