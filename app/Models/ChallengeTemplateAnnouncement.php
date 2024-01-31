<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateAnnouncement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_template_announcements';
    protected $fillable = [
        'challenge_template_id',
        'subject',
        'to_recipient_ids',
        'sent_by',
        'description',
        'schedule_at',
        'status',
        'sent_status',
    ];
}
