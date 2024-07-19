<?php

namespace App\Repositories\Api\Manage\Scorm;

use App\Models\Scorm;
use Illuminate\Http\UploadedFile;

interface ScormInterface
{
    /**
     * @param string       $modelType
     * @param int          $modelId
     * @param UploadedFile $file
     * @param Scorm|null   $existing
     *
     * @return mixed
     */
    public function upload(string $modelType, int $modelId, UploadedFile $file, ?Scorm $existing = null);

    /**
     * @param Scorm $scorm
     *
     * @return mixed
     */
    public function delete(Scorm $scorm);
}
