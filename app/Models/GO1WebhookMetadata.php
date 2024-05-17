<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GO1WebhookMetadata extends Model
{
    use HasFactory;

    protected $table = 'go1_webhook_metadata';

    protected $fillable = [
        'type',
        'fired_at',
        'metadata',
        'go1_user_resource_progress_id',
    ];

    protected $casts = [
        'fired_at' => 'datetime',
        'metadata' => 'object',
    ];
}
