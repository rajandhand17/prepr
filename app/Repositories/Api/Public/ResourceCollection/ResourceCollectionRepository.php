<?php

namespace App\Repositories\Api\Public\ResourceCollection;

use App\Services\Public\ResourceCollectionService;

class ResourceCollectionRepository implements ResourceCollectionInterface
{
    private $resourceCollectionService;

    public function __construct(ResourceCollectionService $resourceCollectionService)
    {
        $this->resourceCollectionService = $resourceCollectionService;
    }

    public function getResourceCollectionList($request)
    {
        try {
            return $this->resourceCollectionService->getResourceCollectionList($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getResourceCollectionBasedOnSlug($slug)
    {
        try {
            return $this->resourceCollectionService->getResourceCollectionBasedOnSlug($slug);
        } catch(\Exception $e) {
            return false;
        }
    }
}
