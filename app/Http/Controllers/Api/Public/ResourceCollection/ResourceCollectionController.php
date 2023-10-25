<?php

namespace App\Http\Controllers\Api\Public\ResourceCollection;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Public\ResourceCollection\ResourceCollectionResource;
use App\Repositories\Api\Public\ResourceCollection\ResourceCollectionRepository;
use Illuminate\Http\Request;

class ResourceCollectionController extends AppBaseController
{
    private $resourceCollectionRepository;
    public function __construct(ResourceCollectionRepository $resourceCollectionRepository)
    {
        $this->resourceCollectionRepository = $resourceCollectionRepository;
    }

    public function index(Request $request){
        try{
            $responseCollectionList = $this->resourceCollectionRepository->getResourceCollectionList($request);
            if ($responseCollectionList) {
                $response = [
                    'total_count'  => $responseCollectionList->total(),
                    'per_page'     => $responseCollectionList->perPage(),
                    'count'        => $responseCollectionList->count(),
                    'current_page' => $responseCollectionList->currentPage(),
                    'total_pages'  => $responseCollectionList->lastPage(),
                    'list'         => ResourceCollectionResource::collection($responseCollectionList),
                ];
                return $this->sendResponse($response, __('responses.found_resource_module_list'));
            }

            return $this->sendError(__('responses.not_found_resource_module_list'), 400);
        }catch (\Exception $e){
            return false;
        }
    }
}
