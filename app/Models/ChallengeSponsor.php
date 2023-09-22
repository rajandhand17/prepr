<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeSponsor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_sponsors';
    protected $fillable = [
        'challenge_id',
        'host_id',
    ];
}
