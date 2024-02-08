<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'comments';

    protected $fillable = [
        'id',
        'user_id',
        'module_id',
        'module_type',
        'comment',
        'attachment',
        'comment_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
