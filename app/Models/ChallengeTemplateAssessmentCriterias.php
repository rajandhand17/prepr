<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateAssessmentCriterias extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_template_assessment_criterias';
    protected $fillable = [
        'challenge_template_id',
        'template_assessment_id',
        'title',
        'description',
        'score',
        'weight',
    ];
}
