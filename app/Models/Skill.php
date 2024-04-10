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
        return $this->hasOne(UserSkills::class, 'skill', 'id');
    }

    public function getChallenges()
    {
        return $this->hasMany(ChallengeSkillsGroupsStack::class, 'foreign_id', 'id')->where('type', '0');
    }

    public function getLabs()
    {
        return $this->hasMany(LabSkillsGroupsStack::class, 'foreign_id', 'id')->where('type', '0');
    }
}
