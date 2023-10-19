<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceModule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_modules';

    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'title',
        'slug',
        'description',
        'media_type',
        'media',
        'privacy',
        'is_auto_created',
        'status',
        'is_global',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function documents()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '0');
    }

    public function videos()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '1');
    }

    public function audios()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '2');
    }

    public function embedded_videos()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '3');
    }

    public function embedded_audios()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '4');
    }

    public function urls()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path', 'social_link_id')->where('type', '=', '5');
    }

    public function images()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->select('id', 'title', 'path')->where('type', '=', '6');
    }

    public function embedded_cover_videos()
    {
        return $this->hasMany(ResourceModuleDetail::class, 'resource_module_id', 'id')->where('type', '=', '7');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(ResourceModuleSocialActivities::class, 'resource_module_id', 'id')->where('like_dislike', '1');
    }

    public function shares()
    {
        return $this->hasMany(ResourceModuleSocialActivities::class, 'resource_module_id', 'id')->where('share', '1');
    }

    public function liked()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ResourceModuleSocialActivities::class, 'resource_module_id', 'id')->where('user_id', auth('api')->user()->id)->where('like_dislike', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'N/A';
    }

    public function favorites()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ResourceModuleSocialActivities::class, 'resource_module_id', 'id')->where('user_id', auth('api')->user()->id)->where('favourite', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'N/A';
    }
}
