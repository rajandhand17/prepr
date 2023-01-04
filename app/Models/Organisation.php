<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisation extends Model
{
    use HasFactory;
    
    use SoftDeletes;

    protected $table = 'organisations';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'vanity_slug',
        'description',
        'cover_image',
        'profile_image',
        'website',
        'facebook',
        'linked',
        'twitter',
        'about',
        'category',
        'latitude',
        'longitude',
        'address',
        'vanity_link',
        'status',
        'associat_lab',
        'associat_challenges',
        'plan',
        'plan_validity',
        'labs_limit',
        'challenges_limit',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
