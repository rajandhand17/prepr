<?php

namespace App\Repositories\Api\Public\AirmeetEvent;

use App\Models\AirmeetEvent;

interface AirmeetEventInterface
{
    /**
     * @param AirmeetEvent $event
     * @param array        $data
     */
    public function getMeetUrl(AirmeetEvent $event, array $data);
}
