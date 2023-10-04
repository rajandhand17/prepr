<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengePathSkillGroupStack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_path_skill_group_stacks';
    protected $fillable = [
        'challenge_path_id',
        'foreign_id',
        'type',
    ];
}
