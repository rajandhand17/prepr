<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserResourceProgressTracking extends Model
{
    use HasFactory;

    protected $table = 'user_resource_progress_tracking';

    protected $fillable = [
        'resource_module_id',
        'user_id',
        'completion_status',
        'lesson_status',
        'score_raw',
        'session_time',
    ];
}
