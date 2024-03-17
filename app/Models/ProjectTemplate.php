<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_templates';
    protected $fillable = [
        'project_id',
        'template_id',
    ];

    public function getTemplatePitches()
    {
        return $this->hasMany(ChallengePitch::class, 'template_id', 'template_id');
    }

    public function getTemplateTasks()
    {
        return $this->hasMany(ChallengeTask::class, 'template_id', 'template_id');
    }
}
