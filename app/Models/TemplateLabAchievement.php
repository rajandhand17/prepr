<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateLabAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_lab_achievements';

    protected $fillable = [
        'template_lab_id',
        'achievement_name',
        'achievement_points',
        'achievement_condition',
        'achievement_image',
    ];
}
