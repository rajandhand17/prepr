<?php

namespace App\Models;

use App\Models\Builder\LabBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lab extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'labs';
    protected $fillable = [
        'type',
        'uuid',
        'language',
        'is_pre_built',
        'user_id',
        'organization_id',
        'category_id',
        'duration_id',
        'level_id',
        'slug',
        'title',
        'description',
        'privacy',
        'media_type',
        'media',
        'status',
        'total_share',
        'is_auto_created',
        'is_ai_created',
        'is_resource_sequential',
        'is_sequential',
        'is_achievement_enabled',
        'is_notification_enabled',
        'is_verified',
        'campus_connect_status',
        'is_accessible',
        'is_live_event_enabled',
    ];

    /**
     * @param $query
     *
     * @return LabBuilder
     */
    public function newEloquentBuilder($query): LabBuilder
    {
        return new LabBuilder($query);
    }

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

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

    public function getCategory()
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
        return $this->hasMany(LabTagsGroups::class, 'lab_id', 'id')->where('type', '1');
    }

    public function component_association()
    {
        return $this->hasMany(ComponentAssociation::class, 'lab_id', 'id');
    }

    public function liked()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(LabSocialActivity::class, 'lab_id', 'id')->where('user_id', auth('api')->user()->id)->where('like_dislike', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function joined()
    {
        if (auth('api')->check()) {
            return $this->hasMany(MemberManagement::class, 'module_id', 'id')->where(['module_type' => '1', 'email' => auth('api')->user()->email])->first();
        }

        return 'NA';
    }

    public function favourite()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(LabSocialActivity::class, 'lab_id', 'id')->where('user_id', auth('api')->user()->id)->where('favourite', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function likes()
    {
        return $this->hasMany(LabSocialActivity::class, 'lab_id', 'id')->where('like_dislike', '1');
    }

    public function shares()
    {
        return $this->hasMany(LabSocialActivity::class, 'lab_id', 'id')->where('share', '1');
    }

    public function members()
    {
        return $this->hasMany(MemberManagement::class, 'module_id', 'id')->where(['module_type' => '1', 'invite_status' => '1']);
    }

    public function durations()
    {
        return $this->belongsTo(Duration::class, 'duration_id', 'id');
    }

    public function levels()
    {
        return $this->belongsTo(Levels::class, 'level_id', 'id');
    }

    /**
     * @return MorphOne
     */
    public function airMeet(): MorphOne
    {
        return $this->morphOne(AirmeetEvent::class, 'model')->latest();
    }

    public function campusConnectOpportunity(): MorphOne
    {
        return $this->morphOne(CampusConnectOpportunity::class, 'model')->latest();
    }

    public function campusConnectStory(): MorphOne
    {
        return $this->morphOne(CampusConnectStory::class, 'model')->latest();
    }

    public function getCampusConnectStatusAttribute($value)
    {
        return config('constants.campus_connect_status_id.'.$value);
    }
}
