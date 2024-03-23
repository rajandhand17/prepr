<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTitleSkill extends Model
{
    protected $table = 'job_title_skills';

    protected $fillable = [
        'job_title_id',
        'skill_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
