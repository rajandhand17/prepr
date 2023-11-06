<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeAnnouncement extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = 'challenge_announcements';
    protected $fillable = [
        'challenge_id',
        'subject',
        'to_recipient_ids',
        'sent_by',
        'description',
        'schedule_at',
        'status',
        'sent_status',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'to_recipient_ids' => 'json',
    ];
}
