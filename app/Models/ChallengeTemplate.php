<?php

namespace App\Models;

use App\Models\Builder\ChallengeTemplateBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_templates';
    protected $fillable = [
        'uuid',
        'language',
        'user_id',
        'organization_id',
        'category_id',
        'duration_id',
        'level_id',
        'slug',
        'title',
        'description_type',
        'description',
        'privacy',
        'media_type',
        'media',
        'status',
        'source_link',
        'agreement',
        'is_notification_enabled',
        'project_privacy',
        'is_pre_built',
        'is_open',
        'is_auto_created',
        'is_ai_created',
        'is_accessible',
        'allow_winner_change',
        'winner_select_date',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @param $query
     *
     * @return ChallengeTemplateBuilder
     */
    public function newEloquentBuilder($query): ChallengeTemplateBuilder
    {
        return new ChallengeTemplateBuilder($query);
    }

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function skills()
    {
        return $this->hasMany(ChallengeTemplateSkillsGroupsStack::class, 'challenge_template_id', 'id')->where('type', '0');
    }

    public function skill_groups()
    {
        return $this->hasMany(ChallengeTemplateSkillsGroupsStack::class, 'challenge_template_id', 'id')->where('type', '1');
    }

    public function skill_stacks()
    {
        return $this->hasMany(ChallengeTemplateSkillsGroupsStack::class, 'challenge_template_id', 'id')->where('type', '2');
    }

    public function durations()
    {
        return $this->belongsTo(Duration::class, 'duration_id', 'id');
    }

    public function levels()
    {
        return $this->belongsTo(Levels::class, 'level_id', 'id');
    }

    public function participation_achievement()
    {
        return $this->hasOne(ChallengeTemplateAchievement::class, 'challenge_template_id', 'id')->where('achievement_type', '0');
    }

    public function incentive_achievement()
    {
        return $this->hasMany(ChallengeTemplateAchievement::class, 'challenge_template_id', 'id')->where('achievement_type', '1');
    }

    public function challenge_requirements()
    {
        return $this->hasOne(ChallengeTemplateRequirement::class, 'challenge_template_id', 'id');
    }

    public function hosts()
    {
        return $this->hasMany(ChallengeTemplateSponsor::class, 'challenge_template_id', 'id');
    }

    public function challenge_assessment_criteria()
    {
        return $this->hasMany(ChallengeTemplateAssessmentCriterias::class, 'challenge_template_id', 'id');
    }

    public function challenge_assessment()
    {
        return $this->hasOne(ChallengeTemplateAssessment::class, 'challenge_template_id', 'id');
    }

    public function challenge_timelines()
    {
        return $this->hasOne(ChallengeTemplateTimeLine::class, 'challenge_template_id', 'id');
    }

    public function challenge_custom_timelines()
    {
        return $this->hasMany(ChallengeTemplateCustomTimeLine::class, 'challenge_template_id', 'id');
    }

    public function challenge_project_template()
    {
        return $this->hasOne(ChallengeTemplateProjectTemplate::class, 'challenge_template_id', 'id');
    }

    public function external_links()
    {
        return $this->hasMany(ChallengeTemplateExternalLink::class, 'challenge_template_id', 'id');
    }

    public function challenge_announcement()
    {
        return $this->hasMany(ChallengeTemplateAnnouncement::class, 'challenge_template_id', 'id');
    }

    public function challenge_association()
    {
        return $this->hasMany(LabMarketplaceComponentAssociations::class, 'challenge_template_id', 'id');
    }

    /**
     * @return MorphOne
     */
    public function scorm(): MorphOne
    {
        return $this->morphOne(Scorm::class, 'model')->latest();
    }
}
