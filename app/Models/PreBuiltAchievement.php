<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreBuiltAchievement extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'fr_CA_title',
        'points',
        'component_type',
        'achievement_image',
        'achievement_type',
        'status',
    ];
}
