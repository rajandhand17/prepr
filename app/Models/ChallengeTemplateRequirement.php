<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateRequirement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_template_requirements';

    protected $fillable = [
        'challenge_template_id',
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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'project_submission_requirement_ids' => 'json',
    ];
}
