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
}
