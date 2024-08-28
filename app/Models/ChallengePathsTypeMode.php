<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengePathsTypeMode extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'challenge_paths_type_modes';
    protected $fillable = [
        'challenge_path_id',
        'type_mode',
        'value',
    ];
}
