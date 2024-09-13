<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollectionSkillsGroupsStack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_collection_skills_groups_stacks';
    protected $fillable = [
        'resource_collection_id',
        'foreign_id',
        'type',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
