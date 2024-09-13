<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationSocialLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'organization_social_links';

    protected $fillable = [
        'organization_id',
        'social_media_link',
        'social_link_id',
    ];
}
