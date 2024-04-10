<?php

namespace App\Repositories\Api\Public\ResourceCollection;

interface ResourceCollectionInterface
{
    public function getResourceCollectionList($request);

    public function addRating($resource_collection_id, $request);
}
