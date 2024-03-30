<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    protected $table = 'job_titles';

    protected $fillable = [
        'uuid',
        'title',
        'fr_CA_title',
        'lightcast_id',
        'pathway_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_job_titles', 'job_title_id', 'user_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_title_skills', 'job_title_id', 'skill_id')
            ->withTimestamps();
    }
}
