<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PitchTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pitch_templates';
    protected $fillable = [
        'title',
        'fr_CA_title',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function challenge_pitch()
    {
        return $this->hasMany(ChallengePitch::class, 'template_id', 'id');
    }

    public function challenge_task()
    {
        return $this->hasMany(ChallengeTask::class, 'template_id', 'id');
    }
}
