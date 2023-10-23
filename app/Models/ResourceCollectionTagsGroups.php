<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollectionTagsGroups extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_collection_tags_groups';

    protected $fillable = [
        'resource_collection_id',
        'foreign_id',
        'type',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
