<?php

namespace App\Models;

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
        'is_accessable',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function component_association(){
        return $this->hasMany(ComponentAssociation::class,'resource_collection_id','id');
    }

    public function durations()
    {
        return $this->belongsTo(Duration::class, 'duration', 'id');
    }

    public function levels()
    {
        return $this->belongsTo(Levels::class, 'level', 'id');
    }

    public function getOrganization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function skills()
    {
        return $this->hasMany(ResourceCollectionSkillsGroupsStack ::class, 'resource_collection_id', 'id')->where('type', '0');
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
        return $this->hasMany(ResourceCollectionSkillsGroupsStack::class, 'resource_collection_id', 'id')->where('type', '0');
    }

    public function tag_groups()
    {
        return $this->hasMany(ResourceCollectionSkillsGroupsStack::class, 'resource_collection_id', 'id')->where('type', '1');
    }
}
