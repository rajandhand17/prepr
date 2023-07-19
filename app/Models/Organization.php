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
        'website',
        'about',
        'category',
        'status',
        'is_verified',
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

    public function getCategory()
    {
        return $this->hasOne(Category::class, 'id');
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
        return $this->hasMany(OrganizationMember::class, 'organization_id', 'id');
    }

    public function labs()
    {
        return $this->hasMany(Lab::class, 'organization_id', 'id');
    }
}
