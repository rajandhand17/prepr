<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateTimeLine extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_template_timelines';

    protected $fillable = [
        'challenge_template_id',
        'timeline_type',
        'start_date',
        'start_date_description',
        'registration_deadline_date',
        'registration_deadline_date_description',
        'submission_deadline_date',
        'submission_deadline_date_description',
        'challenge_duration',
        'flexible_date_number',
        'flexible_date_duration',
        'automatic_alert',
        'flexible_expire_deadline',
    ];
}
