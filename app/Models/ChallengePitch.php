<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengePitch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_pitches';
    protected $fillable = [
        'template_id',
        'title',
        'fr_CA_title',
        'description',
        'fr_CA_description',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getProjectPitchAnswer()
    {
        return $this->hasMany(ProjectPitchValue::class, 'project_pitch_id', 'id');
    }
}
