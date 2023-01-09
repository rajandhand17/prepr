<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlexibleAnnouncement extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table="flexible_announcements";

    protected $fillable=[
        "user_id","challenge_id","custom_date_id","sent_status","title","body","schedule_status","is_completed","announcement_number","announcement_schedule"
    ];

    protected $hidden=[
        "created_at","updated_at","deleted_at"
    ];
}
