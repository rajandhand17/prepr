<?php

namespace App\Repositories\Api\Scorm;

use App\Models\Scorm;
use App\Models\User;
use App\Services\Scorm\ScormService;
use Illuminate\Http\UploadedFile;

class ScormRepository implements ScormInterface
{
    /**
     * @param ScormService $scormService
     */
    public function __construct(protected ScormService $scormService)
    {
    }

    /**
     * @param string $uuid
     * @param User   $scormUser
     *
     * @return Scorm|false|null
     */
    public function getScorm(string $uuid, User $scormUser): false|Scorm|null
    {
        try {
            return $this->scormService->getScorm($uuid, $scormUser);
        } catch (\Exception $exception) {
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
            return false;
        }
    }

    /**
     * @param string       $modelType
     * @param int          $modelId
     * @param UploadedFile $file
     * @param Scorm|null   $existing
     *
     * @return false|Scorm
     */
    public function upload(string $modelType, int $modelId, UploadedFile $file, ?Scorm $existing = null): false|Scorm
    {
        try {
            return $this->scormService->upload($modelType, $modelId, $file, $existing);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param Scorm $scorm
     *
     * @return false|string
     */
    public function generateScormPlayerUrl(Scorm $scorm): false|string
    {
        try {
            return $this->scormService->generateScormPlayerUrl($scorm);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
