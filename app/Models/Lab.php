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

    protected $fillable = [
        'language', 'slug', 'user_id', 'organisation', 'title', 'verification', 'description', 'category', 'privacy', 'mediaType', 'image', 'member', 'member_type', 'latitute', 'longitude', 'address', 'city', 'country', 'challnges', 'lab_skills', 'tag', 'tags', 'status', 'phone', 'company', 'email', 'website', 'facebook', 'linked', 'twitter', 'total_share', 'user_count','is_auto_created', 'res_sequence', 'cha_sequence', 'enable_achievement', 'skill_groups', 'skill_stacks'
    ];

    
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
