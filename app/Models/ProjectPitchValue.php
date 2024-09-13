<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectPitchValue extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_pitch_values';
    protected $fillable = [
        'project_id',
        'pitch_template_id',
        'project_pitch_id',
        'description',
    ];
}
