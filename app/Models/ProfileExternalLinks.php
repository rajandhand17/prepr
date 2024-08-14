<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileExternalLinks extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'profile_external_links';

    protected $fillable = [
        'user_id',
        'social_media_link',
        'social_link_id',
    ];
}
