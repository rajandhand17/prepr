<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeSkillsGroupsStack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_skills_groups_stacks';
    protected $fillable = [
        'challenge_id',
        'foreign_id',
        'type',
    ];
}
