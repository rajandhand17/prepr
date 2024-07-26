<?php

namespace App\Models;

use App\Models\Builder\ResourceCollectionBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_collections';
    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'title',
        'slug',
        'description',
        'status',
        'media_type',
        'media',
        'level',
        'duration',
        'privacy',
        'status',
        'is_accessible',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function newEloquentBuilder($query): ResourceCollectionBuilder
    {
        return new ResourceCollectionBuilder($query);
    }

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function resource_modules()
    {
        return $this->hasMany(ComponentAssociation::class, 'resource_collection_id', 'id')->where('resource_module_id', '!=', null);
    }

    /* Fetching all the component associated data*/
    public function component_association()
    {
        return $this->hasMany(ComponentAssociation::class, 'resource_collection_id', 'id');
    }

    /* Getting all skills groups stacks data*/
    public function skills_groups_stack()
    {
        return $this->hasMany(ResourceCollectionSkillsGroupsStack::class, 'resource_collection_id', 'id');
    }
    public function resource_collection_type_modes()
    {
        return $this->hasOne(ResourceCollectionTypeModes::class, 'resource_collection_id', 'id');
    }
    public function labs()
    {
        return $this->hasMany(ComponentAssociation::class, 'resource_collection_id', 'id')->where('lab_id', '!=', null);
    }

    public function challenges()
    {
        return $this->hasMany(ComponentAssociation::class, 'resource_collection_id', 'id')->where('challenge_id', '!=', null);
    }

    public function getDuration()
    {
        return $this->belongsTo(Duration::class, 'duration', 'id');
    }

    public function getLevel()
    {
        return $this->belongsTo(Levels::class, 'level', 'id');
    }

    public function getOrganization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function skills()
    {
        return $this->hasMany(ResourceCollectionSkillsGroupsStack::class, 'resource_collection_id', 'id')->where('type', '0');
    }

    public function skill_groups()
    {
        return $this->hasMany(ResourceCollectionSkillsGroupsStack::class, 'resource_collection_id', 'id')->where('type', '1');
    }

    public function skill_stacks()
    {
        return $this->hasMany(ResourceCollectionSkillsGroupsStack::class, 'resource_collection_id', 'id')->where('type', '2');
    }

    public function tags()
    {
        return $this->hasMany(ResourceCollectionTagsGroups::class, 'resource_collection_id', 'id')->where('type', '0');
    }

    public function tag_groups()
    {
        return $this->hasMany(ResourceCollectionTagsGroups::class, 'resource_collection_id', 'id')->where('type', '1');
    }

    public function likes()
    {
        return $this->hasMany(ResourceCollectionSocialActivity::class, 'resource_collection_id', 'id')->where('like_dislike', '1');
    }

    public function shares()
    {
        return $this->hasMany(ResourceCollectionSocialActivity::class, 'resource_collection_id', 'id')->where('share', '1');
    }

    public function liked()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ResourceCollectionSocialActivity::class, 'resource_collection_id', 'id')->where('user_id', auth('api')->user()->id)->where('like_dislike', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'N/A';
    }

    public function favorites()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ResourceCollectionSocialActivity::class, 'resource_collection_id', 'id')->where('user_id', auth('api')->user()->id)->where('favourite', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'N/A';
    }

    public function resource_rating()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ResourceCollectionRating::class, 'resource_collection_id', 'id')->where('user_id', auth('api')->user()->id);
        }

        return 'N/A';
    }

    public function resource_collection_completion_status()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ModuleCompletionStatus::class, 'module_id', 'id')->where(['user_id' => auth('api')->user()->id, 'module_type' => '5']);
        }

        return 'N/A';
    }
}
