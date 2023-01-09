<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTag extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table="challange_tags";

    protected $fillable=[
        "challange_id","user_id","tag",

    ];

    protected $hidden=[
        "created_at","updated_at","deleted_at",

    ];
}
