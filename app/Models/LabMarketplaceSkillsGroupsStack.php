<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabMarketplaceSkillsGroupsStack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_marketplace_skills_groups_stack';

    protected $fillable = [
        'lab_marketplace_id',
        'foreign_id',
        'type',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
