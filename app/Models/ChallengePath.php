<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengePath extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_paths';
    protected $fillable = [
        'uuid',
        'language',
        'title',
        'slug',
        'description',
        'user_id',
        'organization_id',
        'category_id',
        'duration_id',
        'level_id',
        'media_type',
        'media',
        'privacy',
        'status',
        'is_achievement_enabled',
        'is_sequential',
        'is_auto_created',
    ];

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url') . $value;
    }
}
