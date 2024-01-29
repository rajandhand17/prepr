<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabMarketplaceAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_marketplace_achievements';

    protected $fillable = [
        'lab_marketplace_id',
        'achievement_name',
        'achievement_points',
        'achievement_condition',
        'achievement_image',
    ];

    protected $casts = [
        'achievement_condition' => 'json',
    ];

    public function getAchievementImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
