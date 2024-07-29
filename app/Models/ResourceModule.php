<?php

namespace App\Models;

use App\Models\Builder\ResourceBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceModule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_modules';

    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'duration_id',
        'level_id',
        'title',
        'slug',
        'description',
        'media_type',
        'media',
        'privacy',
        'is_auto_created',
        'is_ai_created',
        'status',
        'is_global',
        'go1_course_id',
        'go1_metadata',
        'is_accessible',
    ];

    public function newEloquentBuilder($query): ResourceBuilder
    {
        return new ResourceBuilder($query);
    }

    protected $casts = ['go1_metadata' => 'object'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getIsGO1Attribute()
    {
        if ($this->go1_course_id) {
            return true;
        }

        return false;
    }

    public function getMediaAttribute($value)
    {
        if ($this->is_go1) {
            return data_get($this->go1_metadata, 'image');
        }

        return config('site-settings.aws_url').$value;
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function skills_group_stack()
    {
        return $this->hasMany(ResourceModuleSkillsGroupsStack::class, 'resource_module_id', 'id');
    }

    public function resource_module_type_modes()
    {
        return $this->hasOne(ResourceModuleTypeModes::class, 'resource_module_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '0');
    }

    public function videos()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '1');
    }

    public function audios()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '2');
    }

    public function embedded_medias()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'type', 'path')->whereIn('type', ['3', '4']);
    }

    public function urls()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path', 'social_link_id','type')->where('type', '=', '5');
    }

    public function images()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '6');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(ResourceModuleSocialActivities::class, 'resource_module_id', 'id')->where('like_dislike', '1');
    }

    public function shares()
    {
        return $this->hasMany(ResourceModuleSocialActivities::class, 'resource_module_id', 'id')->where('share', '1');
    }

    public function liked()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ResourceModuleSocialActivities::class, 'resource_module_id', 'id')->where('user_id', auth('api')->user()->id)->where('like_dislike', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'N/A';
    }

    public function favorites()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ResourceModuleSocialActivities::class, 'resource_module_id', 'id')->where('user_id', auth('api')->user()->id)->where('favourite', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'N/A';
    }

    public function resource_rating()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ResourceModuleRating::class, 'resource_module_id', 'id')->where('user_id', auth('api')->user()->id);
        }

        return 'N/A';
    }

    public function durations()
    {
        return $this->belongsTo(Duration::class, 'duration_id', 'id');
    }

    public function levels()
    {
        return $this->belongsTo(Levels::class, 'level_id', 'id');
    }

    public function skills()
    {
        return $this->hasMany(ResourceModuleSkillsGroupsStack::class, 'resource_module_id', 'id')->where('type', '0');
    }

    public function skill_groups()
    {
        return $this->hasMany(ResourceModuleSkillsGroupsStack::class, 'resource_module_id', 'id')->where('type', '1');
    }

    public function skill_stacks()
    {
        return $this->hasMany(ResourceModuleSkillsGroupsStack::class, 'resource_module_id', 'id')->where('type', '2');
    }

    public function tags()
    {
        return $this->hasMany(ResourceModuleTagsGroups::class, 'resource_module_id', 'id')->where('type', '0');
    }

    public function tag_groups()
    {
        return $this->hasMany(ResourceModuleTagsGroups::class, 'resource_module_id', 'id')->where('type', '1');
    }

    /**
     * @return MorphOne
     */
    public function scorm(): MorphOne
    {
        return $this->morphOne(Scorm::class, 'model')->latest();
    }

    public function resource_module_completion_status()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ModuleCompletionStatus::class, 'module_id', 'id')->where(['user_id' => auth('api')->user()->id, 'module_type' => '4']);
        }

        return 'N/A';
    }
}
