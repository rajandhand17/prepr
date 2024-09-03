<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_achievements';

    protected $fillable = [
        'user_id', 'title', 'description', 'achievement_type', 'module_id', 'module_title', 'module_parent_id', 'module_parent_title', 'achievement_prize', 'achievement_points', 'achievement_image', 'issue_date', 'valid_date', 'user_notified', 'is_featured', 'promo_code',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getAchievementImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
