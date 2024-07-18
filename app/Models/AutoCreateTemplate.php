<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoCreateTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'auto_create_templates';

    protected $fillable = [
        'language',
        'role_type',
        'role_user_type',
        'lab_id',
        'challenge_id',
        'project_id',
        'lab_group_id',
        'challenge_group_id',
        'invite_labs',
        'invite_challenges',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
