<?php

namespace App\Models;

use App\Models\Builder\ChallengeBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenges';
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

    public function newEloquentBuilder($query): ChallengeBuilder
    {
        return new ChallengeBuilder($query);
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
        return $this->hasMany(ChallengeSkillsGroupsStack::class, 'challenge_id', 'id')->where('type', '0');
    }

    public function skill_groups()
    {
        return $this->hasMany(ChallengeSkillsGroupsStack::class, 'challenge_id', 'id')->where('type', '1');
    }

    public function skill_stacks()
    {
        return $this->hasMany(ChallengeSkillsGroupsStack::class, 'challenge_id', 'id')->where('type', '2');
    }

    public function jobs()
    {
        return $this->hasMany(ChallengeJobTitles::class, 'challenge_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(ChallengeTagsGroups::class, 'challenge_id', 'id')->where('type', '0');
    }

    public function tag_groups()
    {
        return $this->hasMany(ChallengeTagsGroups::class, 'challenge_id', 'id')->where('type', '1');
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
        return $this->hasOne(ChallengeAchievement::class, 'challenge_id', 'id')->where('achievement_type', '0');
    }

    public function incentive_achievement()
    {
        return $this->hasMany(ChallengeAchievement::class, 'challenge_id', 'id')->where('achievement_type', '1');
    }

    public function challenge_requirements()
    {
        return $this->hasOne(ChallengeRequirement::class, 'challenge_id', 'id');
    }

    public function hosts()
    {
        return $this->hasMany(ChallengeSponsor::class, 'challenge_id', 'id');
    }

    public function challenge_assessment_criteria()
    {
        return $this->hasMany(ChallengeAssessmentCriteria::class, 'challenge_id', 'id');
    }

    public function challenge_assessment()
    {
        return $this->hasOne(ChallengeAssessment::class, 'challenge_id', 'id');
    }

    public function challenge_timelines()
    {
        return $this->hasOne(ChallengeTimelines::class, 'challenge_id', 'id');
    }

    public function challenge_custom_timelines()
    {
        return $this->hasMany(ChallengeCustomTimelines::class, 'challenge_id', 'id');
    }

    public function challenge_project_template()
    {
        return $this->hasOne(ChallengeProjectTemplate::class, 'challenge_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(ChallengeSocialActivity::class, 'challenge_id', 'id')->where('like_dislike', '1');
    }

    public function shares()
    {
        return $this->hasMany(ChallengeSocialActivity::class, 'challenge_id', 'id')->where('share', '1');
    }

    public function members()
    {
        return $this->hasMany(MemberManagement::class, 'module_id', 'id')->where(['module_type' => '2', 'invite_status' => '1']);
    }

    public function joined()
    {
        if (auth('api')->check()) {
            return $this->hasMany(MemberManagement::class, 'module_id', 'id')->where(['module_type' => '2', 'email' => auth('api')->user()->email])->first();
        }

        return 'NA';
    }

    public function liked()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ChallengeSocialActivity::class, 'challenge_id', 'id')->where('user_id', auth('api')->user()->id)->where('like_dislike', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function favourite()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ChallengeSocialActivity::class, 'challenge_id', 'id')->where('user_id', auth('api')->user()->id)->where('favourite', '1')->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function external_links()
    {
        return $this->hasMany(ChallengeExternalLink::class, 'challenge_id', 'id');
    }

    public function challenge_announcement()
    {
        return $this->hasMany(ChallengeAnnouncement::class, 'challenge_id', 'id');
    }

    public function submitted_projects()
    {
        return $this->hasMany(Project::class, 'challenge_id', 'id')->where('is_submitted', '1');
    }

    public function challenge_association()
    {
        return $this->hasMany(ComponentAssociation::class, 'challenge_id', 'id');
    }

    public function campusConnectOpportunity(): MorphOne
    {
        return $this->morphOne(CampusConnectOpportunity::class, 'model');
    }

    public function campusConnectStory(): MorphOne
    {
        return $this->morphOne(CampusConnectStory::class, 'model');
    }

    public function getCampusConnectStatusAttribute($value)
    {
        return config('constants.campus_connect_status_id.'.$value);
    }
}
