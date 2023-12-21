<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateTimeLine extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_challenge_timelines';

    protected $fillable = [
        'template_challenge_id',
        'timeline_type',
        'open_call_date',
        'open_call_date_description',
        'last_call_date',
        'last_call_date_description',
        'application_deadline_date',
        'application_deadline_date_description',
        'submission_deadline_date',
        'submission_deadline_date_description',
        'challenge_duration',
        'flexible_date_number',
        'flexible_date_duration',
        'automatic_alert',
        'flexible_expire_deadline',
    ];
}
