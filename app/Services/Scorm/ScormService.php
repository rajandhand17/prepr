<?php

namespace App\Services\Scorm;

use App\Helpers\UtilityHelper;
use App\Models\Scorm;
use App\Models\User;
use App\Services\Scorm\Utils\ScormArchiver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ScormService
{
    /**
     * @param ScormArchiver         $scormArchiver
     * @param ScormScoService       $scormScoService
     * @param ScormUserTokenService $scormUserTokenService
     */
    public function __construct(
        protected ScormArchiver $scormArchiver,
        protected ScormScoService $scormScoService,
        protected ScormUserTokenService $scormUserTokenService
    ) {
    }

    public function upload(string $modelType, int $modelId, UploadedFile $file, ?Scorm $existing = null): false|Scorm
    {
        DB::beginTransaction();

        try {
            $scormData = $this->scormArchiver->parseScormArchive($file);
            /**
             * REMOVES THE OLD SCORM FILE IF EXISTS.
             */
            if ($existing) {
                $deletePrevious = $this->delete($existing);
                if (!$deletePrevious) {
                    DB::rollBack();

                    return false;
                }
            }
            /**
             * @var Scorm $scorm
             */
            $scorm = Scorm::query()->create([
                'model_id'         => $modelId,
                'model_type'       => $modelType,
                'uuid'             => data_get($scormData, 'uuid'),
                'title'            => data_get($scormData, 'title'),
                'version'          => data_get($scormData, 'version'),
                'origin_file'      => $file->getClientOriginalName(),
                'origin_file_mime' => $file->getClientMimeType(),
                'entry_url'        => data_get($scormData, 'entry_url'),
            ]);
            /*** STORE SCORM SCOS */
            $content = $this->scormScoService->bulkStore($scorm, data_get($scormData, 'scos', []));
            $file = $this->scormArchiver->storeScormContent(data_get($scormData, 'file_path'), $file);
            if (!$file || !$content) {
                DB::rollBack();

                return false;
            }
        } catch (\Exception $exception) {
            DB::rollBack();

            return false;
        }
        DB::commit();

        return $scorm;
    }

    /**
     * @param Scorm $scorm
     *
     * @return bool
     */
    public function delete(Scorm $scorm): bool
    {
        try {
            $scorm->delete();
            $scormFolderDelete = $this->scormArchiver->deleteScormFolder($scorm->uuid);
            if (!$scormFolderDelete) {
                return false;
            }

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string $uuid
     * @param User   $scormUser
     *
     * @return Scorm|false|null
     */
    public function getScorm(string $uuid, User $scormUser): null|Scorm|false
    {
        try {
            /** @var Scorm $scorm */
            $scorm = Scorm::query()
                ->where(['uuid' => $uuid])
                ->with(['scos' => function ($query) use ($scormUser) {
                    $query->with(['scorm', 'children.scoTracking' => function ($query) use ($scormUser) {
                        $query->where('user_id', '=', $scormUser->id);
                    }, 'scoTracking' => function ($query) use ($scormUser) {
                        $query->where('user_id', '=', $scormUser->id);
                    }])->where('sco_parent_id', '=', null);
                }])->firstOrFail();

            return $scorm;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string $url
     *
     * @return array|false
     */
    public function generateScormProxy(string $url): false|array
    {
        try {
            /**
             * ADDING THE SCORM FILE DIRECTOR.
             */
            $url = sprintf('%s/%s', $this->scormArchiver->scormRootDirectory, $url);
            if (!$this->checkExtension($url, ['js']) && !$this->checkExtension($url, ['json']) && str_contains($url, 'html') === false) {
                logger()->info($this->scormArchiver->storage->url($url));

                return [
                    'url' => $this->scormArchiver->storage->url($url),
                ];
            }
            $contentType = 'text/html';
            if ($this->checkExtension($url, ['js'])) {
                $contentType = 'text/javascript';
            } elseif ($this->checkExtension($url, ['json'])) {
                $contentType = 'application/json';
            } elseif ($this->checkExtension($url, ['css'])) {
                $contentType = 'text/css';
            }

            return [
                'binary'       => $this->scormArchiver->storage->get($url),
                'content_type' => $contentType,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param       $url
     * @param array $allowed
     *
     * @return bool
     */
    public function checkExtension($url, array $allowed = []): bool
    {
        try {
            $parsedUrl = parse_url($url);
            if (isset($parsedUrl['path'])) {
                $path = pathinfo($parsedUrl['path'], PATHINFO_BASENAME);
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                return in_array($extension, $allowed);
            }

            return false;
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
            /** @var User $authUser */
            $authUser = auth()->user();
            $scormUserToken = $this->scormUserTokenService->getUserScormToken($authUser);
            if (!$scormUserToken) {
                return false;
            }

            return sprintf(
                '%s/scorm-player/%s?tracking_id=%s&language=%s',
                UtilityHelper::sanitizeUrl(config('scorm.scorm_app_base_url', '')),
                $scorm->uuid,
                $scormUserToken->token,
                app()->getLocale()
            );
        } catch (\Exception $exception) {
            return false;
        }
    }
}
