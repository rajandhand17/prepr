<?php

namespace App\Models\Accessor;

use App\Helpers\UtilityHelper;
use App\Models\LabSocialActivity;

trait LabAccessor
{
    /**
     * @return array|null
     */
    public function getFormattedLabDurationAttribute(): ?array
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
     * @return array|null
     */
    public function getFormattedLabLevelAttribute(): ?array
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
    public function getFormattedLabPrivacyAttribute(): mixed
    {
        $privacy = $this->privacy;
        if (!$privacy) {
            return null;
        }

        $privacyMap = [
            '0' => 'no',
            '1' => 'yes',
        ];

        return data_get($privacyMap, $privacy);
    }

    public function getFormattedFavouriteCountAttribute(): int
    {
        return $this->hasMany(LabSocialActivity::class, 'lab_id', 'id')->where('favourite', '1')->count();
    }
}
