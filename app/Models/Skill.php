<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'skills';

    protected $fillable = [
        'title',
        'fr_CA_title',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function user_pinned()
    {
        if (auth('api')->check()) {
            return $this->hasOne(UserSkills::class, 'skill', 'id')->where('user_id', auth('api')->user()->id);
        }

        return 'N/A';
    }

    public function getChallenges()
    {
        return $this->hasMany(ChallengeSkillsGroupsStack::class, 'foreign_id', 'id')->where('type', '0');
    }

    public function getLabs()
    {
        return $this->hasMany(LabSkillsGroupsStack::class, 'foreign_id', 'id')->where('type', '0');
    }

    public function getRelatedResources()
    {
        return $this->hasMany(ResourceCollectionSkillsGroupsStack::class, 'foreign_id', 'id')->where('type', '0');
    }

    public function saved_skill()
    {
        if (auth('api')->check()) {
            return $this->hasOne(UserSkills::class, 'skill', 'id')->where('user_id', auth('api')->user()->id);
        }

        return 'NA';
    }
}
