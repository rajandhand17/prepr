<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateExternalLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_challenge_external_links';

    protected $fillable = [
        'template_challenge_id',
        'social_media_link',
        'social_link_id',
    ];
}
