<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laratrust\Models\LaratrustTeam;

class Organization extends LaratrustTeam
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'organizations';

    protected $fillable = [
        'uuid',
        'title',
        'display_name',
        'description',
        'language',
        'user_id',
        'slug',
        'cover_image',
        'profile_image',
        'custom_url',
        'website',
        'about',
        'category',
        'status',
        'is_verified',
        'is_onboarding_completed',
        'business_challenge_tacklings',
        'magnet_community_id',
        'total_employees',

    ];

    public function getCoverImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function getProfileImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    // public function getCustomUrlAttribute($value)
    // {
    //     $frontendSiteUrl = config('site-settings.frontend_site_url');
    //     return "{$frontendSiteUrl}organization/{$value}";
    // }

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->hasMany(OrganizationAddress::class, 'organization_id', 'id');
    }

    public function members()
    {
        return $this->hasMany(MemberManagement::class, 'module_id', 'id')->where(['module_type' => '0', 'invite_status' => '1']);
    }

    public function organizationMembers()
    {
        return $this->hasMany(OrganizationMember::class, 'organization_id', 'id');
    }

    public function labs()
    {
        return $this->hasMany(Lab::class, 'organization_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(OrganizationSocialActivities::class, 'organization_id', 'id')->where('like_dislike', '1');
    }

    public function followers()
    {
        return $this->hasMany(OrganizationSocialActivities::class, 'organization_id', 'id')->where('follow_unfollow', '1');
    }

    public function shares()
    {
        return $this->hasMany(OrganizationSocialActivities::class, 'organization_id', 'id')->where('share', '1');
    }

    public function liked()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(OrganizationSocialActivities::class, 'organization_id', 'id')->where('user_id', auth('api')->user()->id)->where('like_dislike', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function followed()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(OrganizationSocialActivities::class, 'organization_id', 'id')->where('user_id', auth('api')->user()->id)->where('follow_unfollow', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function favourite()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(OrganizationSocialActivities::class, 'organization_id', 'id')->where('user_id', auth('api')->user()->id)->where('favourite', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function labs_count()
    {
        return $this->hasMany(Lab::class, 'organization_id', 'id');
    }

    public function lab_programs_count()
    {
        return $this->hasMany(LabProgram::class, 'organization_id', 'id');
    }

    public function challenges_count()
    {
        return $this->hasMany(Challenge::class, 'organization_id', 'id');
    }

    public function challenge_paths_count()
    {
        return $this->hasMany(ChallengePath::class, 'organization_id', 'id');
    }

    public function resource_modules_count()
    {
        return $this->hasMany(ResourceModule::class, 'organization_id', 'id');
    }

    public function resource_collections_count()
    {
        return $this->hasMany(ResourceCollection::class, 'organization_id', 'id');
    }

    public function resource_groups_count()
    {
        return $this->hasMany(ResourceGroup::class, 'organization_id', 'id');
    }

    public function chargebee_details()
    {
        return $this->hasOne(ChargebeeSubscription::class, 'organization_id', 'id');
    }

    public function external_links()
    {
        return $this->hasMany(OrganizationExternalLink::class, 'organization_id', 'id');
    }

    public function customization_login_register()
    {
        return $this->hasOne(OrganizationCustomization::class, 'organization_id', 'id');
    }

    public function organizationType()
    {
        return $this->hasMany(OrganizationTypeMode::class, 'organization_id', 'id')->where(['type_mode' => '0']);
    }
}
