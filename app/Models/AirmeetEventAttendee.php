<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirmeetEventAttendee extends Model
{
    use HasFactory;

    use HasFactory;

    protected $fillable = [
        'airmeet_event_id',
        'airmeet_event_uuid',
        'attendee_id',
        'event_url',
    ];
}
