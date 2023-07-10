<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabChallenges extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_challenges';

    protected $fillable = [
        'lab_id',
        'challenge_id',
        'challenge_path_id',
        'status',
        'sequence_no',
    ];
}
