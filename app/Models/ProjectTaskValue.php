<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTaskValue extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_task_values';
    protected $fillable = [
        'project_id',
        'task_template_id',
        'project_task_id',
        'status',
        'completed_date',
    ];
}
