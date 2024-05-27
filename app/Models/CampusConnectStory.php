<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CampusConnectStory extends Model
{
    use HasFactory;

    protected $table = 'campus_connect_story';

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $fillable = ['language', 'model_type', 'model_id', 'metadata', 'ep_id'];

    public function campusConnectStory(): MorphTo
    {
        return $this->morphTo('model');
    }
}
