<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeRequirement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_requirements';
    protected $fillable = [
        'challenge_id',
        'min_rank',
        'min_points',
        'project_submission_requirement_ids',
        'max_project_submission',
        'min_experience',
        'min_imported_badges',
        'min_achievement_counts',
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
