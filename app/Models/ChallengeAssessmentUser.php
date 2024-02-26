<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeAssessmentUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_assessment_users';
    protected $fillable = [
        'criteria_id',
        'project_id',
        'user_id',
        'score',
        'weight',
        'comment',
        'criteria_comment',
    ];
}
