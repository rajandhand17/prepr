<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Go1WebhookMetadata extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'fired_at',
        'metadata',
        'user_resource_progress_tracking_id',
    ];

    protected $casts = [
        'fired_at' => 'datetime',
        'metadata' => 'object',
    ];
}
