<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabMarketplaceProgram extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_marketplace_program';

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
        'is_auto_created',
        'is_achievement_enabled',
        'is_sequential',
    ];
}
