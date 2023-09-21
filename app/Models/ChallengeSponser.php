<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeSponser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_sponsers';
    protected $fillable = [
        'challenge_id',
        'host_id',
    ];
}
