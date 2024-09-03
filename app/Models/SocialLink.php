<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialLink extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'social_links';

    protected $fillable = [
        'title',
        'icon',
    ];

    public function getIconAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
