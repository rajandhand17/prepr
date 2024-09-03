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
        'assessment_id',
        'title',
        'description',
        'score',
        'weight',
    ];

    public function assessment()
    {
        return $this->belongsTo(ChallengeAssessment::class, 'assessment_id', 'id');
    }

    public function challengeAssessmentUser()
    {
        return $this->hasMany(ChallengeAssessmentUser::class, 'criteria_id', 'id');
    }
}
