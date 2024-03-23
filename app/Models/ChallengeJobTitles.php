<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeJobTitles extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_job_title_association';
    protected $fillable = [
        'challenge_id',
        'job_title_id',
    ];
}
