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
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_jobs', 'job_id', 'user_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_id', 'skill_id')
            ->withTimestamps();
    }
}
