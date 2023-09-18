<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabProgramsAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_programs_achievements';

    protected $fillable = [
        'lab_program_id',
        'achievement_name',
        'achievement_points',
        'achievement_image',
    ];

    public function getAchievementImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
