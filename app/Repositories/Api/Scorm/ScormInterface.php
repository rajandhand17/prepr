<?php

namespace App\Repositories\Api\Scorm;

use App\Models\Scorm;
use App\Models\User;
use Illuminate\Http\UploadedFile;

interface ScormInterface
{
    /**
     * @param string $uuid
     * @param User $scormUser
     * @return mixed
     */
    public function getScorm(string $uuid, User $scormUser);

    /**
     * @param string $url
     * @return mixed
     */
    public function generateScormProxy(string $url);

    /**
     * @param string $modelType
     * @param int $modelId
     * @param UploadedFile $file
     * @param Scorm|null $existing
     * @return mixed
     */
    public function upload(string $modelType, int $modelId, UploadedFile $file, ?Scorm $existing = null);

    /**
     * @param Scorm $scorm
     * @return mixed
     */
    public function generateScormPlayerUrl(Scorm $scorm);
}
