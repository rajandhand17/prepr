<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPoint extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table="user_point";
    protected $fillable=[
        "id",
        "user_id",
        "type",
        "point",
        "date",
        "status",
    ];
}
