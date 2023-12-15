<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateChallengeAssessment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_challenge_assessments';
    protected $fillable = [
        'template_challenge_id',
        'assessment_type',
        'visibility',
        'members_email',
        'guidelines',
        'attachments',
    ];

    protected $hidden = [
        'created_at', 'deleted_at', 'updated_at',
    ];
}
