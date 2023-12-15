<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateChallengeAssessmentCriterias extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_challenge_assessment_criterias';
    protected $fillable = [
        'template_challenge_id',
        'title',
        'score',
        'weight',
    ];
}
