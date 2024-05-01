<?php

namespace App\Repositories\Api\Manage\Scorm;

use App\Models\Scorm;
use App\Models\User;
use App\Services\Manage\Scorm\ScormService;
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

}
