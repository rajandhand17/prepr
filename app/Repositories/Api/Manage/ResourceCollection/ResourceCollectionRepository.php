<?php

namespace App\Repositories\Api\Manage\ResourceCollection;

use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\ResourceCollectionService;
use DB;

class ResourceCollectionRepository implements ResourceCollectionInterface
{
    private $resourceCollectionService;

    private $componentAssociationService;

    public function __construct(ResourceCollectionService $resourceCollectionService, ComponentAssociationService $componentAssociationService)
    {
        $this->resourceCollectionService =$resourceCollectionService;
        $this->componentAssociationService=$componentAssociationService;
    }

    public function createResourceCollection($request, $upload_cover_image){
        try{

            $createResourceCollection = DB::transaction(function () use ($request, $upload_cover_image) {
                $createResourceCollection=$this->resourceCollectionService->createResourceCollection($request,$upload_cover_image);
                $componentAssociation=$this->componentAssociationService->createResourceCollectionAssociation($request,$createResourceCollection->id);
                return[
                    "createResourceCollection"=>$createResourceCollection,
                    "componentAssociation"=>$componentAssociation,
                ];
            });
            if($createResourceCollection['createResourceCollection'] && $createResourceCollection['componentAssociation']){
                DB::commit();
                return true;
            }
            DB::rollback();
            return false;
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
