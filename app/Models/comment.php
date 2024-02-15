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

    public function comments_reply(){
       return $this->hasMany(comment::class,'comment_id','id');
    }

    public function users(){
        return $this->hasone(User::class,'id','user_id');
    }
}
