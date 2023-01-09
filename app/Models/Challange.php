<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challange extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table="challanges";

    protected $fillable=[
         'language', 'slug', 'user_id', 'organisation', 'host_id', 'title', 'verification', 'description', 'sourcelink', 'category', 'challange_skill', 'challange_tag', 'tags', 'associat_lab', 'status', 'project_privacy', 'deadline', 'dates', 'flexibleDateNumber', 'flexibleExpireDateDuration', 'flexibleExpireDate', 'automaticAlert', 'submission_deadline_date_desc', 'min_ranks', 'min_points', 'projectSubmissionRequirements', 'submitProject', 'maxProjectSubmission', 'maxAssociatedProjects', 'completeEducationProgram', 'completeExperience', 'requirementProgram', 'minExperience', 'minImportedBadges', 'minAchievementTrophies', 'additional_info', 'application_deadline', 'length', 'last_registration_date', 'call_date', 'mediaType', 'cover_image', 'privacy', 'total_share', 'is_completed', 'agreement', 'type', 'price', 'code', 'subject_line', 'email_message', 'application_dateline_desc', 'call_date_desc', 'last_registration_date_desc', 'assessment_date', 'assessment_date_desc', 'published'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
