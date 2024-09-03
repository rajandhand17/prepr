<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateExternalLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_template_external_links';

    protected $fillable = [
        'challenge_template_id',
        'social_media_link',
        'social_link_id',
    ];
}
