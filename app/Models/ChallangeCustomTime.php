<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallangeCustomTime extends Model
{
    use HasFactory;

    use SoftDeletes;


    protected $table="challange_custom_times";

    protected $fillable=[
         'challenge_id','title','date','description','scheduleAnnouncement','customDateNumber','customDateDuration',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
