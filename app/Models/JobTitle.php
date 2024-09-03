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

    public function related_labs()
    {
        return $this->hasMany(LabSkillsGroupsStack::class, 'foreign_id', 'id')->where('type', '0');
    }

    public function related_challenge()
    {
        return $this->hasMany(ChallengeSkillsGroupsStack::class, 'foreign_id', 'id')->where('type', '0');
    }

    public function related_resources()
    {
        return $this->hasMany(ResourceModuleSkillsGroupsStack::class, 'foreign_id', 'id')->where('type', '0');
    }

    public function saved_jobs()
    {
        if (auth('api')->check()) {
            return ($this->hasOne(UserJobTitle::class, 'job_title_id', 'id')->where('user_id', auth('api')->user()->id)->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function pinned()
    {
        if (auth('api')->check()) {
            return $this->hasOne(UserJobTitle::class, 'job_title_id', 'id')->where('user_id', auth('api')->user()->id);
        }

        return 'NA';
    }
}
