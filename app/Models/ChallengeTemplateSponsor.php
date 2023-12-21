<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateSponsor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_challenge_sponsors';

    protected $fillable = [
        'template_challenge_id',
        'host_id',
    ];
}
