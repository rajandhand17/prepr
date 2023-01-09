<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeAssessment extends Model
{
    use HasFactory;

    use SoftDeletes;
    
    protected $table="challange_assessments";

    protected $fillable=[
         'challenge_id', 'assessment_type', 'visibility', 'members', 'guidline', 'attachment',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
}
