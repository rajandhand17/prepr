<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTask extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_tasks';
    protected $fillable = [
        'project_id',
        'task_template_id',
    ];
}
