<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateSponsor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_template_sponsors';

    protected $fillable = [
        'challenge_template_id',
        'host_id',
    ];
}
