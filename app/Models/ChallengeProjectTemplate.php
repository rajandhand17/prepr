<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeProjectTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_project_templates';
    protected $fillable = [
        'challenge_id',
        'template_id',
    ];
}
