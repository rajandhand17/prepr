<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateChallengeRequirement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_challenge_requirements';
    protected $fillable = [
        'template_challenge_id',
        'min_rank',
        'min_points',
        'project_submission_requirement_ids',
        'max_project_submission',
        'max_project_associate',
        'min_experience',
        'min_imported_badges',
        'min_achievement_counts',
        'allow_submit_project',
        'requirement_program',
        'complete_education_program',
        'complete_experience',
        'additional_requirements',
    ];
}
