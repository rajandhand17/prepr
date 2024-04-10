<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTask extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_tasks';
    protected $fillable = [
        'template_id',
        'title',
        'fr_CA_title',
    ];

    public function getProjectTaskAnswer()
    {
        return $this->hasMany(ProjectTaskValue::class, 'project_task_id', 'id');
    }
}
