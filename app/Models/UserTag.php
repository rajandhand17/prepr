<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTag extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_tags';

    protected $fillable = [
        'id',
        'user_id',
        'tag_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
