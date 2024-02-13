<?php

namespace App\Models;

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
        'view_enabled',
        'download_enabled',
        'media_type',
        'media',
        'status',
        'challenge_id',
        'lab_id',
        'category_id',
        'type_id',
        'industry_id',
        'stage_id',
        'vertical_id',
        'status_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function getProjectTemplate()
    {
        return $this->hasOne(ChallengeProjectTemplate::class, 'challenge_id', 'challenge_id');
    }

    public function getProjectFile()
    {
        return $this->hasMany(ProjectFile::class, 'project_id', 'id');
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
            return $this->hasOne(ProjectMemberManagement::class, 'project_id', 'id')->where('invite_status', '1')->where('invitee_id', auth('api')->user()->id)->orWhere('email', auth('api')->user()->email)->first();
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

    public function shares()
    {
        if (auth('api')->check()) {
            return $this->hasMany(ProjectSocialActivity::class, 'project_id', 'id')->where(['user_id' => auth('api')->user()->id, 'share' => '1'])->count();
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
}
