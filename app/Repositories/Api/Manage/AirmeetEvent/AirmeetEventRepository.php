<?php

namespace App\Repositories\Api\Manage\AirmeetEvent;

use App\Helpers\UtilityHelper;
use App\Services\Manage\AirmeetEventService;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;

class AirmeetEventRepository implements AirmeetEventInterface
{
    /**
     * @param AirmeetEventService $airmeetEventService
     */
    public function __construct(protected AirmeetEventService $airmeetEventService)
    {
    }

    /**
     * @param string $eventId
     *
     * @return false|PromiseInterface|Response
     */
    public function getVerifiedEventDetails(string $eventId): false|PromiseInterface|Response
    {
        try {
            return $this->airmeetEventService->getVerifiedEventDetails($eventId);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param string $model
     * @param int    $model_id
     * @param array  $data
     *
     * @return false|Builder|Model
     */
    public function createUpdateEvent(string $model, int $model_id, array $data): Model|Builder|false
    {
        try {
            return $this->airmeetEventService->createUpdateEvent($model, $model_id, $data);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
