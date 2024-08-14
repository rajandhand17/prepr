<?php

namespace App\Models;

use App\Models\Builder\ResourceGroupBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_groups';
    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'category_id',
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
        'is_auto_created',
        'is_accessible',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function newEloquentBuilder($query): ResourceGroupBuilder
    {
        return new ResourceGroupBuilder($query);
    }

    public function getMediaAttribute($value)
    {
        if ($this->media_type == '1') {
            return $value;
        }

        return config('site-settings.aws_url').$value;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getDuration()
    {
        return $this->belongsTo(Duration::class, 'duration', 'id');
    }

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function getLevel()
    {
        return $this->belongsTo(Levels::class, 'level', 'id');
    }

    public function getOrganization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function skills_group_stack()
    {
        return $this->hasMany(ResourceGroupSkillsGroupStack::class, 'resource_group_id', 'id');
    }

    public function resource_group_achievement()
    {
        return $this->hasOne(ResourceGroupAchievement::class, 'resource_group_id', 'id');
    }

    public function resource_group_type_mode()
    {
        return $this->hasMany(ResourceGroupTypeModes::class, 'resource_group_id', 'id');
    }
    public function resource_group_type()
    {
        return $this->hasMany(ResourceGroupTypeModes::class, 'resource_group_id', 'id')->where('type_mode','0');
    }
    public function resource_group_mode()
    {
        return $this->hasMany(ResourceGroupTypeModes::class, 'resource_group_id', 'id')->where('type_mode','1');
    }
    public function component_association()
    {
        return $this->hasMany(ComponentAssociation::class, 'resource_group_id', 'id');
    }

    public function skills()
    {
        return $this->hasMany(ResourceGroupSkillsGroupStack::class, 'resource_group_id', 'id')->where('type', '0');
    }

    public function skill_groups()
    {
        return $this->hasMany(ResourceGroupSkillsGroupStack::class, 'resource_group_id', 'id')->where('type', '1');
    }

    public function skill_stacks()
    {
        return $this->hasMany(ResourceGroupSkillsGroupStack::class, 'resource_group_id', 'id')->where('type', '2');
    }

    public function tags()
    {
        return $this->hasMany(ResourceGroupTagGroups::class, 'resource_group_id', 'id')->where('type', '0');
    }

    public function tag_groups()
    {
        return $this->hasMany(ResourceGroupTagGroups::class, 'resource_group_id', 'id')->where('type', '1');
    }

    public function achievement()
    {
        return $this->hasOne(ResourceGroupAchievement::class, 'resource_group_id', 'id');
    }

    public function resource_collection()
    {
        return $this->hasMany(ComponentAssociation::class, 'resource_group_id', 'id')->where('resource_collection_id', '!=', null);
    }

    public function resource_modules()
    {
        return $this->hasMany(ComponentAssociation::class, 'resource_group_id', 'id')->where('resource_module_id', '!=', null);
    }

    public function favourite()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ResourceGroupSocialActivity::class, 'resource_group_id', 'id')->where(['favourite' => '1', 'user_id' => auth('api')->user()->id])->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function liked()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ResourceGroupSocialActivity::class, 'resource_group_id', 'id')->where(['like_dislike' => '1', 'user_id' => auth('api')->user()->id])->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function liked_count()
    {
        if (auth('api')->check()) {
            return $this->hasMany(ResourceGroupSocialActivity::class, 'resource_group_id', 'id')->where(['like_dislike' => '1'])->count();
        }

        return 'NA';
    }

    public function resource_rating()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ResourceGroupRating::class, 'resource_group_id', 'id')->where('user_id', auth('api')->user()->id);
        }

        return 'N/A';
    }

    public function resource_group_completion_status()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ModuleCompletionStatus::class, 'module_id', 'id')->where(['user_id' => auth('api')->user()->id, 'module_type' => '6']);
        }

        return 'N/A';
    }
}
