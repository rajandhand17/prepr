<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateChallenge extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table="template_challenges";

    protected $fillable = [
        'preferred_language',
        'first_name',
        'last_name',
        'full_name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'country_code',
        'phone_number',
        'two_factor_verification',
        'otp',
        'profile_image',
        'user_points',
        'user_rank',
        'verified_user',
        'verify_token',
        'referral_code',
        'is_profile_completed',
        'remember_token',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
