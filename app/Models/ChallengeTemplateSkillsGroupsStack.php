<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateSkillsGroupsStack extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'template_challenge_skills_groups_stacks';

    protected $fillable = ['template_challenge_id', 'foreign_id', 'type'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
