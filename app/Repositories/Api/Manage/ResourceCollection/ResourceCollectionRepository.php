<?php

namespace App\Repositories\Api\Manage\ResourceCollection;

use App\Services\Manage\ResourceCollectionService;
use DB;

class ResourceCollectionRepository implements ResourceCollectionInterface
{
    private $resourceCollectionService;

    public function __construct(ResourceCollectionService $resourceCollectionService)
    {
        $this->resourceCollectionService =$resourceCollectionService;
    }

    public function createResourceCollection($request, $upload_media){
        try{
            return $this->resourceCollectionService->createResourceCollection($request,$upload_media);
        }catch(\Exception $e){
            return false;
        }
    }

    public function uploadResourceCollectionCoverImage($cover_image){
        try{
         return  $this->resourceCollectionService->uploadResourceCollectionCoverImage($cover_image);
        }catch(\Exception $e){
            return false;
        }
    }

}
