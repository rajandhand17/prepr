<?php

namespace App\Models;

use App\Models\Builder\LabMarketPlaceBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabMarketplace extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_marketplace';

    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'category_id',
        'type',
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

    /**
     * @param $query
     *
     * @return LabMarketPlaceBuilder
     */
    public function newEloquentBuilder($query): LabMarketPlaceBuilder
    {
        return new LabMarketPlaceBuilder($query);
    }

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function address()
    {
        return $this->hasOne(LabMarketplaceAddress::class, 'lab_marketplace_id', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function achievement()
    {
        return $this->hasOne(LabMarketplaceAchievement::class, 'lab_marketplace_id', 'id');
    }

    public function external_links()
    {
        return $this->hasMany(LabMarketplaceExternalLink::class, 'lab_marketplace_id', 'id');
    }

    public function skills()
    {
        return $this->hasMany(LabMarketplaceSkillsGroupsStack::class, 'lab_marketplace_id', 'id')->where('type', '0');
    }

    public function skill_groups()
    {
        return $this->hasMany(LabMarketplaceSkillsGroupsStack::class, 'lab_marketplace_id', 'id')->where('type', '1');
    }

    public function skill_stacks()
    {
        return $this->hasMany(LabMarketplaceSkillsGroupsStack::class, 'lab_marketplace_id', 'id')->where('type', '2');
    }

    public function tags()
    {
        return $this->hasMany(LabMarketplaceTagsGroups::class, 'lab_marketplace_id', 'id')->where('type', '0');
    }

    public function tag_groups()
    {
        return $this->hasMany(LabMarketplaceTagsGroups::class, 'lab_marketplace_id', 'id')->where('type', '1');
    }

    public function component_association()
    {
        return $this->hasMany(LabMarketplaceComponentAssociations::class, 'lab_marketplace_id', 'id');
    }

    public function durations()
    {
        return $this->belongsTo(Duration::class, 'duration_id', 'id');
    }

    public function levels()
    {
        return $this->belongsTo(Levels::class, 'level_id', 'id');
    }
}
