<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeAnnouncementRecipient extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = 'challenge_announcement_recipients';
    protected $fillable = [
        'title',
        'fr_CA_title',
    ];
}
