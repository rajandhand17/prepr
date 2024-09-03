<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CampusConnectOpportunity extends Model
{
    use HasFactory;

    protected $table = 'campus_connect_opportunity';

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $fillable = ['model_id', 'model_type', 'metadata', 'language', 'ep_id'];

    public function campusConnectOpportunity(): MorphTo
    {
        return $this->morphTo('model');
    }
}
