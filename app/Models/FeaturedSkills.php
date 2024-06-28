<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedSkills extends Model
{
    use HasFactory;

    protected $table = 'featured_skills';

    protected $fillable = [
        'skill_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
