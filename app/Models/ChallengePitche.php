<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengePitche extends Model
{
    use HasFactory;

    use SoftDeletes;
    
    protected $table="challange_pitches";

    protected $fillable=[
        "challenge_id","pitch_template_id",


    ];
    
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
