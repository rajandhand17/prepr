<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateAssessment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_template_assessments';
    protected $fillable = [
        'challenge_template_id',
        'assessment_type',
        'visibility',
        'members_email',
        'guidelines',
        'attachments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'members_email' => 'json',
    ];

    protected $hidden = [
        'created_at', 'deleted_at', 'updated_at',
    ];
}
