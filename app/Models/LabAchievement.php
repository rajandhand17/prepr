<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;

    

    protected $table="lab_achievements";

    protected $fillable = [
        'lab_id',
        'achievement_name',
        'achievement_points',
        'achievement_condition',
        'achievement_image',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
