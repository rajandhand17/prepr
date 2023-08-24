<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PitchTemplate extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'pitch_templates';

    protected $fillable = [
        'title',
        'challenge_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
