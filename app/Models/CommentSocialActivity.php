<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentSocialActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'comment_social_activities';

    protected $fillable = [
        'id',
        'comment_id',
        'comment_type',
        'user_id',
        'like_dislikes',
    ];
}
