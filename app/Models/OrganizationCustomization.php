<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationCustomization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'organization_customizations';
    protected $fillable = [
        'organization_id',
        'enable_custom_login_and_registration',
        'use_main_org_logo',
        'custom_logo_image',
        'custom_hero_image',
        'custom_background_color',
    ];

    public function getCustomLogoImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function getCustomHeroImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
