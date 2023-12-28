<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSkills extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_skills';

    protected $fillable = [
        'user_id', 'skill', 'pinned',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
