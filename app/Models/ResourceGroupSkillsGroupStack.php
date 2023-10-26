<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceGroupSkillsGroupStack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_groups_skills_groups_stacks';

    protected $fillable = [
        'resource_group_id',
        'foreign_id',
        'type',
    ];
}
