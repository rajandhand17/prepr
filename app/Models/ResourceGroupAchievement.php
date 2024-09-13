<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceGroupAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_group_achievements';
    protected $fillable = [
        'resource_group_id',
        'achievement_name',
        'achievement_points',
        'achievement_image',
    ];

    public function getAchievementImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
