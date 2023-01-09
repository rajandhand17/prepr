<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeAnnouncement extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'challenge_announcement';

    protected $fillable=[
        'user_id','challenge_id','customDateId','sent_status','title','body','schedule_status','announcementNumber','announcementSchedule','date','time','recipients','is_completed','is_send',
    ];

    protected $hidden=[
        "created_at","updated_at","deleted_at",
    ];

}
