<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_template_achievements';

    protected $fillable = [
        'challenge_template_id',
        'achievement_type',
        'achievement_name',
        'achievement_prize',
        'achievement_points',
        'achievement_image',
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at',
    ];
}
