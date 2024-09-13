<?php

namespace App\Repositories\Api\Public\AirmeetEvent;

use App\Helpers\UtilityHelper;
use App\Models\AirmeetEvent;
use App\Services\Public\AirmeetEventService;

class AirmeetEventRepository implements AirmeetEventInterface
{
    /**
     * @param AirmeetEventService $airmeetEventService
     */
    public function __construct(protected AirmeetEventService $airmeetEventService)
    {
    }

    /**
     * @param AirmeetEvent $event
     * @param array        $data
     *
     * @return false|string
     */
    public function getMeetUrl(AirmeetEvent $event, array $data): false|string
    {
        try {
            return $this->airmeetEventService->getMeetUrl($event, $data);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
