<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GO1UserResourceProgress extends Model
{
    use HasFactory;

    protected $table = 'go1_user_resource_progress';

    protected $fillable = [
        'resource_module_id',
        'user_id',
        'completion_status',
        'lesson_status',
        'score_raw',
        'session_time',
    ];
}
