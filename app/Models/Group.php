<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable=[
        "title",
        "description",
        "organisation",
        "category",
        "type",
        "challenge_id",
        "lab_id",
        "resource_id",
        "collection_id",
        "user_id",
        "group_image",
        "privacy",
        "status",
        "challenge_privacy",
        "privacy_project",
        "published",
        "prize",
        "points",
        "trophy",
        "is_auto_created",
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
