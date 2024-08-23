<?php

namespace App\Models\Accessor;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeSocialActivity;

trait ChallengeAccessor
{
    /**
     * @return array|null
     */
    public function getChallengeDurationAttribute(): ?array
    {
        $duration = $this->durations;

        if (!$duration) {
            return null;
        }

        return [
            'id'    => $duration->id,
            'title' => UtilityHelper::isEngLocale() ? $duration->title : $duration->fr_CA_title,
        ];
    }

    /**
     * @return string
     */
    public function getChallengeStatusAttribute(): string
    {
        $status = $this->status;

        return $status === 0 ? 'Draft' : 'Published';
    }

    /**
     * @return array|null
     */
    public function getChallengeLevelAttribute(): ?array
    {
        $level = $this->levels;

        if (!$level) {
            return null;
        }

        return [
            'id'    => $level->id,
            'title' => UtilityHelper::isEngLocale() ? $level->title : $level->fr_CA_title,
        ];
    }

    /**
     * @return array|mixed|null
     */
    public function getChallengePrivacyAttribute(): mixed
    {
        $privacy = $this->privacy;

        $privacyMap = [
            '0' => 'no',
            '1' => 'yes',
        ];

        return data_get($privacyMap, $privacy);
    }

    /**
     * @return array|mixed|null
     */
    public function getChallengeTypeAttribute(): mixed
    {
        $types = $this->challengeType()->get(); //$this->challengeType gives error

        if (!$types) {
            return null;
        }

        $typeMap = [
            '0' => 'assess',
            '1' => 'onboard',
            '2' => 'engage',
            '3' => 'grow',
            '4' => 'team',
            '5' => 'individual',
        ];

        return $types->map(function ($type) use ($typeMap) {
            return data_get($typeMap, $type->value, null);
        })->filter()->values()->toArray();
    }

    /**
     * @return array|mixed|null
     */
    public function getChallengeModeAttribute(): mixed
    {
        $modes = $this->challengeMode()->get(); //$this->challengeMode gives error

        if (!$modes) {
            return null;
        }

        $modeMap = [
            '0' => 'type',
            '1' => 'mode',
        ];

        return $modes->map(function ($mode) use ($modeMap) {
            return data_get($modeMap, $mode->type_mode, null);
        })->filter()->values()->toArray();
    }

    /**
     * @return int
     */
    public function getFavouriteCountAttribute(): int
    {
        return $this->hasMany(ChallengeSocialActivity::class, 'challenge_id', 'id')->where('favourite', '1')->count();
    }

    public function getAchievementPointsAttribute(): int
    {
        return $this->achievements()->sum('achievement_points');
    }
}
