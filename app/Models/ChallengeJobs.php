<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeJobs extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_jobs';
    protected $fillable = [
        'challenge_id',
        'job_id',
    ];
}
