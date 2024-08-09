<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LastVisitedActivityModule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'last_visited_activity_modules';
    protected $fillable = [
        'user_id',
        'module_id',
        'module_type',
    ];
}
