<?php

namespace App\Repositories\Api\Manage\Resource;

use App\Services\Manage\ResourceService;

class ResourceRepository implements ResourceInterface
{
    protected $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    public function store($request)
    {
        $addExternalLinks = $this->resourceService->store($request);
    }
}
