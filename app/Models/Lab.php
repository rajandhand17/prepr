<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lab extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'labs';
    //protected $softCascade  =['component_associations','lab_acheivements','lab_address','lab_challenges','lab_external_links','lab_skills_groups_stack','lab_tags_groups'];
    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'category_id',
        'slug',
        'title',
        'description',
        'privacy',
        'media_type',
        'media',
        'status',
        'total_share',
        'is_auto_created',
        'is_resource_sequential',
        'is_sequential',
        'is_achievement_enabled',
        'is_notification_enabled',
        'is_verified',
    ];

    public function address()
    {
        return $this->hasOne(LabAddress::class, 'lab_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function achievement()
    {
        return $this->hasOne(LabAcheivement::class, 'lab_id', 'id');
    }

    public function external_links()
    {
        return $this->hasMany(LabExternalLinks::class, 'lab_id', 'id');
    }

    public function skills()
    {
        return $this->hasMany(LabSkillsGroupsStack::class, 'lab_id', 'id')->where('type', '0');
    }

    public function skill_groups()
    {
        return $this->hasMany(LabSkillsGroupsStack::class, 'lab_id', 'id')->where('type', '1');
    }

    public function skill_stacks()
    {
        return $this->hasMany(LabSkillsGroupsStack::class, 'lab_id', 'id')->where('type', '2');
    }

    public function tags()
    {
        return $this->hasMany(LabTagsGroups::class, 'lab_id', 'id')->where('type', '0');
    }

    public function tag_groups()
    {
        return $this->hasMany(LabTagsGroups::class, 'lab_id', 'id');
    }

    public function component_association()
    {
        return $this->hasMany(ComponentAssociation::class, 'lab_id', 'id');
    }
}
