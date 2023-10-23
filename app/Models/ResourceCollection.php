<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_collections';
    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'title',
        'slug',
        'description',
        'status',
        'media_type',
        'media',
        'level',
        'duration',
        'privacy',
        'status',
        'is_accessable',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
