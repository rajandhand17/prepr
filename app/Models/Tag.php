<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'tags';

    protected $fillable = [
        'title', 'fr_CA_title', 'tag_image', 'fr_CA_tag_image', 'components',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getTagImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
