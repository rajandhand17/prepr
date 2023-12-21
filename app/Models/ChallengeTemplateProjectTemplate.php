<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateProjectTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_challenge_project_templates';
    protected $fillable = [
        'template_challenge_id',
        'template_id',
    ];
}
