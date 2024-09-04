<?php

namespace App\Models\Accessor;

use App\Helpers\UtilityHelper;
use App\Repositories\Api\Public\Scorm\ScormRepository;

trait ChallengeAccessor
{
    /**
     * @return array|null
     */
    public function getFormattedChallengeDurationAttribute(): ?array
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
    public function getFormattedChallengeStatusAttribute(): string
    {
        $status = $this->status;

        return $status === 0 ? 'Draft' : 'Published';
    }

    /**
     * @return array|null
     */
    public function getFormattedChallengeLevelAttribute(): ?array
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
    public function getFormattedChallengePrivacyAttribute(): mixed
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
    public function getFormattedChallengeTypeAttribute(): mixed
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
    public function getFormattedChallengeModeAttribute(): mixed
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

    public function getFormattedAchievementPointsAttribute(): int
    {
        return $this->achievements()->sum('achievement_points');
    }

    public function getFormattedScormUrlAttribute(): false|string|null
    {
        /** @var ScormRepository $scormRepository */
        $scormRepository = app()->make(ScormRepository::class);
        $scorm = $this->scorm;
        if ($scorm) {
            return $scormRepository->generateScormPlayerUrl($scorm, false);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getFormattedSubmissionDeadlineDateAttribute(): string
    {
        if ($this->challenge_timelines) {
            if ($this->challenge_timelines->timeline_type == '0') {
                return $this->challenge_timelines->flexible_expire_deadline;
            } else if ($this->challenge_timelines->timeline_type == '1') {
                return $this->challenge_timelines->submission_deadline_date;
            }
        }

        return '-';
    }
}
