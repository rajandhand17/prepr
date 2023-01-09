<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ChallengePrice extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $table = 'challange_prices';

    protected $fillable = [
        'name',
        'type',
        'challenge_id',
        'prize',
        'trophy',
        'points',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}