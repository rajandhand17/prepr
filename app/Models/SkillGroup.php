<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkillGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'skill_groups';

    protected $fillable = [
        'title',
        'fr_CA_title',
        'skill_stacks',
        'skills',
        'description',
        'fr_CA_description',
    ];
    protected $casts = [
        'skills'       => 'json',
        'skill_stacks' => 'json',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
