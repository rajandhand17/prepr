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

    public function getAttachmentsAttribute($value)
    {
        return config('site-settings.aws_url') . $value;
    }
}
