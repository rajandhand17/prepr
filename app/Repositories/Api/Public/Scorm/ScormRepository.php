<?php

namespace App\Repositories\Api\Public\Scorm;

use App\Helpers\UtilityHelper;
use App\Models\Scorm;
use App\Models\User;
use App\Services\Public\Scorm\ScormService;

class ScormRepository implements ScormInterface
{
    /**
     * @param ScormService $scormService
     */
    public function __construct(protected ScormService $scormService)
    {
    }

    /**
     * @param string    $uuid
     * @param User|null $scormUser
     *
     * @return Scorm|false|null
     */
    public function getScorm(string $uuid, User|null $scormUser): false|Scorm|null
    {
        try {
            return $this->scormService->getScorm($uuid, $scormUser);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param string $url
     *
     * @return false|array
     */
    public function generateScormProxy(string $url): false|array
    {
        try {
            return $this->scormService->generateScormProxy($url);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param Scorm $scorm
     *
     * @return false|string
     */
    public function generateScormPlayerUrl(Scorm $scorm, $trackingId = true): false|string
    {
        try {
            return $this->scormService->generateScormPlayerUrl($scorm, $trackingId);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
