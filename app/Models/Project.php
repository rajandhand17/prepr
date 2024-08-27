<?php

namespace App\Models;

use App\Models\Builder\ProjectBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'projects';
    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'title',
        'slug',
        'description',
        'is_view_enabled',
        'is_download_enabled',
        'media_type',
        'media',
        'privacy',
        'recruiting_status',
        'challenge_id',
        'lab_id',
        'is_submitted',
        'total_share',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function newEloquentBuilder($query): ProjectBuilder
    {
        return new ProjectBuilder($query);
    }

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function getProjectTemplate()
    {
        return $this->hasOne(ChallengeProjectTemplate::class, 'challenge_id', 'challenge_id');
    }

    public function getProjectIdBasedTemplate()
    {
        return $this->hasOne(ProjectTemplate::class, 'project_id', 'id')->latest();
    }

    public function getProjectImages()
    {
        return $this->hasMany(ProjectFile::class, 'project_id', 'id')->where('type', 'image');
    }

    public function getProjectDocs()
    {
        return $this->hasMany(ProjectFile::class, 'project_id', 'id')->where('type', 'docs');
    }

    public function getProjectVideos()
    {
        return $this->hasMany(ProjectFile::class, 'project_id', 'id')->where('type', 'video');
    }

    public function getProjectAudios()
    {
        return $this->hasMany(ProjectFile::class, 'project_id', 'id')->where('type', 'audio');
    }

    public function external_links()
    {
        return $this->hasMany(ProjectExternalLink::class, 'project_id', 'id');
    }

    public function getProjectAdditionalInfo()
    {
        return $this->hasOne(ProjectAdditionalInfo::class, 'project_id', 'id');
    }

    public function getJoinedStatus()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ProjectMemberManagement::class, 'project_id', 'id')->where(['invite_status' => '1', 'email' => auth('api')->user()->email])->first();
        }

        return null;
    }

    public function getMembersCount()
    {
        return $this->hasOne(ProjectMemberManagement::class, 'project_id', 'id')->where('invite_status', '1')->count();
    }

    public function likes()
    {
        if (auth('api')->check()) {
            return $this->hasMany(ProjectSocialActivity::class, 'project_id', 'id')->where(['user_id' => auth('api')->user()->id, 'like_dislike' => '1'])->count();
        }

        return 0;
    }

    public function votes()
    {
        if (auth('api')->check()) {
            return $this->hasMany(ProjectSocialActivity::class, 'project_id', 'id')->where(['vote' => '1'])->count();
        }

        return 0;
    }

    public function shares()
    {
        if (auth('api')->check()) {
            return $this->hasMany(ProjectSocialActivity::class, 'project_id', 'id')->where(['share' => '1'])->count();
        }

        return 0;
    }

    public function favourite()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ProjectSocialActivity::class, 'project_id', 'id')->where(['user_id' => auth('api')->user()->id, 'favourite' => '1'])->count() > 0) ? 'yes' : 'no';
        }

        return 'no';
    }

    public function getProjectAssessment()
    {
        return $this->hasOne(ChallengeAssessment::class, 'challenge_id', 'challenge_id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function challengeAssessmentUsers()
    {
        return $this->hasMany(ChallengeAssessmentUser::class, 'project_id', 'id');
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,                         // Final related model (User)
            ChallengeAssessmentUser::class,     // Intermediate model (ChallengeAssessmentUser)
            'project_id',                       // Foreign key on ChallengeAssessmentUser that references this model's id
            'id',                            // Foreign key on User that references ChallengeAssessmentUser's user_id
            'id',                               // Local key on this model
            'user_id'                     // Local key on ChallengeAssessmentUser that references User's id
        )->distinct();
    }

    public function skills()
    {
        return $this->hasMany(ProjectSkill::class, 'project_id', 'id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMemberManagement::class, 'project_id', 'id')->where('invite_status', '1');
    }

    public function member()
    {
        return $this->hasOne(ProjectMemberManagement::class, 'project_id', 'id')->where('email', auth()->user()->email);
    }

    public function history()
    {
        return $this->hasMany(ProjectHistory::class, 'project_id', 'id');
    }

    public function challenge()
    {
        return $this->hasOne(Challenge::class, 'id', 'challenge_id');
    }

    public function friendRequest()
    {
        return $this->hasOne(ProjectMemberManagement::class, 'project_id', 'id')->where('email', auth()->user()->email);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getStage()
    {
        return $this->belongsTo(ProjectStage::class, 'stage_id', 'id');
    }

    public function getStatus()
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id', 'id');
    }

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function getVertical()
    {
        return $this->belongsTo(ProjectVertical::class, 'vertical_id', 'id');
    }

    public function getIndustry()
    {
        return $this->belongsTo(ProjectIndustry::class, 'industry_id', 'id');
    }

    public function getType()
    {
        return $this->belongsTo(ProjectType::class, 'type_id', 'id');
    }
}
