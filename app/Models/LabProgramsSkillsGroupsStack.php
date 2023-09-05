<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabProgramsSkillsGroupsStack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_programs_skills_groups_stack';

    protected $fillable = [
        'lab_program_id',
        'foreign_id',
        'type',
    ];
}
