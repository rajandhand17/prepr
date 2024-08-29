<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeAssessment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_assessments';
    protected $fillable = [
        'challenge_id',
        'assessment_type',
        'visibility',
        'members_email',
        'guidelines',
        'attachments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'members_email' => 'json',
    ];

    public function getAttachmentsAttribute($value)
    {
        if ($value == null) {
            return null;
        }

        return config('site-settings.aws_url').$value;
    }

    public function getAssessmentCriterias()
    {
        return $this->hasMany(ChallengeAssessmentCriteria::class, 'assessment_id', 'id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'challenge_id', 'challenge_id');
    }

    public function getMemberNamesAttribute()
    {
        $emails = $this->members_email;
        $names = User::whereIn('email', $emails)->pluck('full_name');

        return $names->implode(', ');
    }
}
