<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabProgramSocialActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_program_social_activities';

    protected $fillable = [
        'user_id',
        'lab_program_id',
        'follow_unfollow',
        'share',
        'favourite',
    ];
}
