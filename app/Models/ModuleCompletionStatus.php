<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleCompletionStatus extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'module_completion_statuses';
    protected $fillable = [
        'user_id',
        'module_id',
        'module_type',
        'status',
        'is_completed',
        'percentage',
    ];
}
