<?php

namespace App\Models;

use App\Models\Builder\LabProgramBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabProgram extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_programs';

    protected $fillable = [
        'language',
        'uuid',
        'title',
        'slug',
        'description',
        'user_id',
        'organization_id',
        'category_id',
        'duration_id',
        'level_id',
        'media_type',
        'media',
        'privacy',
        'status',
        'is_auto_created',
        'is_achievement_enabled',
        'is_sequential',
        'is_accessible',
    ];

    public function newEloquentBuilder($query): LabProgramBuilder
    {
        return new LabProgramBuilder($query);
    }

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function component_association()
    {
        return $this->hasMany(ComponentAssociation::class, 'lab_program_id', 'id');
    }

    public function getOrganization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
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
            return ($this->hasMany(LabProgramSocialActivity::class, 'lab_program_id', 'id')->where(['favourite' => '1', 'user_id' => auth('api')->user()->id])->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function liked()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(LabProgramSocialActivity::class, 'lab_program_id', 'id')->where(['like_dislike' => '1', 'user_id' => auth('api')->user()->id])->count() > 0) ? 'Yes' : 'No';
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

    public function labs()
    {
        return $this->hasMany(ComponentAssociation::class, 'lab_program_id', 'id')->where('lab_id', '!=', null);
    }

    public function lab_program_completion_status()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ModuleCompletionStatus::class, 'module_id', 'id')->where(['user_id' => auth('api')->user()->id, 'module_type' => '1']);
        }

        return 'N/A';
    }

    /**
     * @return HasMany
     */
    public function labProgramProgress(): HasMany
    {
        return $this->hasMany(ModuleCompletionStatus::class, 'module_id')->where('module_type', '=', '1');
    }

    public function labProgramType()
    {
        return $this->hasMany(LabProgramTypeModes::class, 'lab_program_id', 'id')->where('type_mode', '0')->pluck('value');
    }

    public function labProgramMode()
    {
        return $this->hasOne(LabProgramTypeModes::class, 'lab_program_id', 'id')->where('type_mode', '1')->pluck('value');
    }

    public function isJoined()
    {
        if (auth('api')->check()) {
            return $this->hasMany(MemberManagement::class, 'module_id', 'id')->where(['module_type' => '3', 'email' => auth('api')->user()->email])->first();
        }

        return 'NA';
    }
}
