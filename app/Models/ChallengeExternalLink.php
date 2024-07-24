<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeExternalLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_external_links';

    protected $fillable = [
        'challenge_id',
        'social_media_link',
        'social_link_id',
    ];

    public function social_link()
    {
        return $this->belongsTo(SocialLink::class, 'social_link_id', 'id');
    }
}
