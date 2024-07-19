<?php

namespace App\Repositories\Api\Manage\Scorm;

use App\Helpers\UtilityHelper;
use App\Models\Scorm;
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
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param Scorm $scorm
     *
     * @return bool
     */
    public function delete(Scorm $scorm): bool
    {
        try {
            return $this->scormService->delete($scorm);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
