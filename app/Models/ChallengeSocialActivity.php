<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeSocialActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_social_activities';
    protected $fillable = [
        'user_id',
        'challenge_id',
        'like_dislike',
        'share',
        'favourite',
    ];
}
