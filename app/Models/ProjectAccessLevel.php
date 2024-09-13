<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectAccessLevel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_access_levels';
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];
}
