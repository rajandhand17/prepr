<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabAcheivement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_acheivements';

    protected $fillable = [
        'lab_id',
        'achievement_name',
        'achievement_points',
        'achievement_condition',
        'achievement_image',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'achievement_condition' => 'json',
    ];

    public function getAchievementImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
