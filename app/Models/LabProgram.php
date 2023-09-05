<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabProgram extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_programs';

    protected $fillable = [
        'language',
        'title',
        'description',
        'lab_id',
        'user_id',
        'media',
        'privacy',
        'status',
        'is_auto_created',
        'prize',
        'points',
        'trophy',
    ];


    public function component_association()
    {
        return $this->hasMany(ComponentAssociation::class, 'lab_program_id', 'id');
    }
    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function achievement()
    {
        return $this->hasOne(LabProgramsAchievement::class, 'lab_program_id', 'id');
    }
    public function tags()
    {
        return $this->hasMany(LabProgramsTagsGroups::class, 'lab_program_id', 'id')->where('type', '0');
    }

    public function tag_groups()
    {
        return $this->hasMany(LabProgramsTagsGroups::class, 'lab_program_id', 'id')->where('type', '1');
    }
    public function skills()
    {
        return $this->hasMany(LabProgramsSkillsGroupsStack::class, 'lab_program_id', 'id')->where('type', '0');
    }

    public function skill_groups()
    {
        return $this->hasMany(LabProgramsSkillsGroupsStack::class, 'lab_program_id', 'id')->where('type', '1');
    }

    public function skill_stacks()
    {
        return $this->hasMany(LabProgramsSkillsGroupsStack::class, 'lab_program_id', 'id')->where('type', '2');
    }

    public function durations()
    {
        return $this->belongsTo(Duration::class, 'duration_id', 'id');
    }

    public function levels()
    {
        return $this->belongsTo(Levels::class, 'level_id', 'id');
    }

    public function favourite()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(LabProgramSocialActivity::class, 'lab_program_id', 'id')->where('user_id', auth('api')->user()->id)->where('favourite', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function likes()
    {
        return $this->hasMany(LabProgramSocialActivity::class, 'lab_program_id', 'id')->where('like_dislike', '1');
    }

    public function shares()
    {
        return $this->hasMany(LabProgramSocialActivity::class, 'lab_program_id', 'id')->where('share', '1');
    }
}
