<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AchievementConditionList extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'achievement_condition_lists';

    protected $fillable = [
        'title',
        'fr_CA_title',
    ];

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
