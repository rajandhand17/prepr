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
        'comment',
        'criteria_comment',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function challengeAssessmentCriteria()
    {
        return $this->belongsTo(ChallengeAssessmentCriteria::class, 'criteria_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}
