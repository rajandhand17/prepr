<?php

namespace App\Models;

use App\Models\Builder\ChallengePathBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengePath extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_paths';
    protected $fillable = [
        'uuid',
        'language',
        'title',
        'slug',
        'description',
        'user_id',
        'organization_id',
        'category_id',
        'duration_id',
        'level_id',
        'media_type',
        'media',
        'privacy',
        'status',
        'is_achievement_enabled',
        'is_sequential',
        'is_auto_created',
        'is_accessible',
    ];

    /**
     * @param $query
     *
     * @return ChallengePathBuilder
     */
    public function newEloquentBuilder($query): ChallengePathBuilder
    {
        return new ChallengePathBuilder($query);
    }

    public function getMediaAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function component_association()
    {
        return $this->hasMany(ComponentAssociation::class, 'challenge_path_id', 'id');
    }

    public function getOrganization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    public function getCategory()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function achievement()
    {
        return $this->hasOne(ChallengePathAchievement::class, 'challenge_path_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(ChallengePathTagGroup::class, 'challenge_path_id', 'id')->where('type', '0');
    }

    public function tag_groups()
    {
        return $this->hasMany(ChallengePathTagGroup::class, 'challenge_path_id', 'id')->where('type', '1');
    }

    public function skills()
    {
        return $this->hasMany(ChallengePathSkillGroupStack::class, 'challenge_path_id', 'id')->where('type', '0');
    }

    public function skill_groups()
    {
        return $this->hasMany(ChallengePathSkillGroupStack::class, 'challenge_path_id', 'id')->where('type', '1');
    }

    public function skill_stacks()
    {
        return $this->hasMany(ChallengePathSkillGroupStack::class, 'challenge_path_id', 'id')->where('type', '2');
    }

    public function durations()
    {
        return $this->belongsTo(Duration::class, 'duration_id', 'id');
    }

    public function levels()
    {
        return $this->belongsTo(Levels::class, 'level_id', 'id');
    }

    public function favourite()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ChallengePathSocialActivity::class, 'challenge_path_id', 'id')->where(['favourite' => '1', 'user_id' => auth('api')->user()->id])->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function liked()
    {
        if (auth('api')->check()) {
            return ($this->hasMany(ChallengePathSocialActivity::class, 'challenge_path_id', 'id')->where(['like_dislike' => '1', 'user_id' => auth('api')->user()->id])->count() > 0) ? 'Yes' : 'No';
        }

        return 'NA';
    }

    public function shares()
    {
        return $this->hasMany(ChallengePathSocialActivity::class, 'challenge_path_id', 'id')->where('share', '1');
    }
}
