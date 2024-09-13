<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceModuleDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_module_details';

    protected $fillable = [
        'resource_module_id',
        'title',
        'type',
        'path',
        'social_link_id',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getPathAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function social_link()
    {
        return $this->belongsTo(SocialLink::class, 'social_link_id', 'id');
    }
}
