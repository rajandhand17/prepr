<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeFlexibleAnnouncement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_flexible_announcements';
    protected $fillable = [
        'challenge_id',
        'challenge_custom_timeline_id',
        'custom_announcement_type',
        'custom_announcement_number',
        'custom_announcement_duration',
        'custom_announcement_description',
    ];
}
