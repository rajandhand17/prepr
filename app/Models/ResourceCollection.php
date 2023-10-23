<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_collections';
    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'title',
        'slug',
        'description',
        'status',
        'media_type',
        'media',
        'level',
        'duration',
        'privacy',
        'status',
        'is_accessable',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function resource_modules(){
        return $this->hasMany(ComponentAssociation::class,'id','resource_collection_id')->where('resource_module_id','!=',null);
    }
}
