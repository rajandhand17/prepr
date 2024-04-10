<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabSocialActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_social_activities';

    protected $fillable = [
        'user_id',
        'lab_id',
        'like_dislike',
        'favourite',
        'share',
    ];
}
