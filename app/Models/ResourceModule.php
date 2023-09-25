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
        'status',
        'is_global',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function resource_module_document(){
        return $this->hasMany(ResourceModuleDetail::class,'resource_module_id','id')->where('type', '=','0');
    }
    public function resource_module_video(){
        return $this->hasMany(ResourceModuleDetail::class,'resource_module_id','id')->where('type', '=','1');
    }
    public function resource_module_audio(){
        return $this->hasMany(ResourceModuleDetail::class,'resource_module_id','id')->where('type', '=','2');
    }

    public function resource_module_embedded(){
        return $this->hasMany(ResourceModuleDetail::class,'resource_module_id','id')->where('type', '=','3');
    }
    public function resource_module_embedded_audio(){
        return $this->hasMany(ResourceModuleDetail::class,'resource_module_id','id')->where('type', '=','4');
    }
    public function resource_module_url(){
        return $this->hasMany(ResourceModuleDetail::class,'resource_module_id','id')->select('title','path','social_link_id')->where('type', '=','5');
    }

    public function resource_module_image(){
        return $this->hasMany(ResourceModuleDetail::class,'resource_module_id','id')->where('type', '=','6');
    }

    public function resource_module_Embedded_Cover_Video(){
        return $this->hasMany(ResourceModuleDetail::class,'resource_module_id','id')->where('type', '=','7');
    }
    public function users(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
