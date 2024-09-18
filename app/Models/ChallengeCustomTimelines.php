<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeCustomTimelines extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_custom_timelines';
    protected $fillable = [
        'challenge_id',
        'custom_timelines_title',
        'custom_timelines_number',
        'custom_timelines_description',
        'custom_timelines_duration',
        'schedule_custom_notify',
    ];

    public function challengeScheduleCustomAnnouncement()
    {
        return $this->hasOne(ChallengeFlexibleAnnouncement::class, 'challenge_custom_timeline_id', 'id')->where('challenge_id', $this->challenge_id);
    }
}
