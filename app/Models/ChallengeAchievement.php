<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_achievements';
    protected $fillable = [
        'challenge_id',
        'achievement_type',
        'achievement_name',
        'achievement_prize',
        'achievement_points',
        'achievement_image',
    ];

    public function getAchievementImageAttribute($value)
    {
        return !empty($value) ? config('site-settings.aws_url').$value : null;
    }
}
