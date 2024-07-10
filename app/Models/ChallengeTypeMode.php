<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTypeMode extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_type_modes';
    protected $fillable = [
        'challenge_id',
        'type_mode',
        'value',
    ];

    public function challenge()
    {
        return $this->belongsTo(Challenge::class, 'challenge_id', 'id');
    }
}
