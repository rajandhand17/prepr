<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeAssessmentCriteria extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_assessment_criterias';
    protected $fillable = [
        'challenge_id',
        'title',
        'score',
        'weight',
    ];
}
