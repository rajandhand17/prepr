<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengePathTemplateAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_path_template_achievements';
    protected $fillable = [
        'challenge_path_template_id',
        'achievement_name',
        'achievement_points',
        'achievement_image',
    ];

    public function getAchievementImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
