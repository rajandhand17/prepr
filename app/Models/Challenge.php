<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenges';
    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'category_id',
        'slug',
        'title',
        'description',
        'privacy',
        'media_type',
        'media',
        'status',
        'source_link',
        'agreement',
        'is_notification_enabled',
        'project_privacy',
        'is_open',
        'is_auto_created',
    ];
}
