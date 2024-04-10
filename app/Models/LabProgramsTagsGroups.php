<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabProgramsTagsGroups extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_programs_tags_groups';

    protected $fillable = [
        'lab_program_id',
        'foreign_id',
        'type',
    ];
}
