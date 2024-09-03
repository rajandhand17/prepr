<?php

namespace App\Repositories\Api\Manage\AirmeetEvent;

interface AirmeetEventInterface
{
    /**
     * @param string $eventId
     */
    public function getVerifiedEventDetails(string $eventId);

    /**
     * @param string $model
     * @param int    $model_id
     * @param array  $data
     */
    public function createUpdateEvent(string $model, int $model_id, array $data);
}
