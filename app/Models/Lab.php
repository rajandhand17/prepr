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

    protected $fillable=[
            "language",
            "user_id",
            "organization_id",
            "category_id",
            "slug",
            "title",
            "description",
            "privacy",
            "media_type",
            "media",
            "status",
            "total_share",
            "is_auto_created",
            "is_resource_sequential",
            "is_sequential",
            "is_achievement_enabled",
            "is_notification_enabled",
            "is_verified",
            "uuid"
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
}
