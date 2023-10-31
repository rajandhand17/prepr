<?php

namespace App\Http\Controllers\Api\Public\ResourceGroup;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\ResourceCollection\ResourceCollectionResource;
use App\Http\Resources\Public\ResourceGroup\ResourceGroupResource;
use App\Repositories\Api\Public\ResourceGroup\ResourceGroupRepository;
use Illuminate\Http\Request;

class ResourceGroupController extends AppBaseController
{
    private $resourceGroupRepository;

    public function __construct(ResourceGroupRepository $resourceGroupRepository)
    {
        $this->resourceGroupRepository = $resourceGroupRepository;
    }

    public function index(Request $request)
    {
        try {
            $responseGroupList = $this->resourceGroupRepository->getResourceGroupList($request);
            if ($responseGroupList) {
                $response = [
                    'total_count'  => $responseGroupList->total(),
                    'per_page'     => $responseGroupList->perPage(),
                    'count'        => $responseGroupList->count(),
                    'current_page' => $responseGroupList->currentPage(),
                    'total_pages'  => $responseGroupList->lastPage(),
                    'list'         => ResourceGroupResource::collection($responseGroupList),
                ];
                return $this->sendResponse($response, __('responses.found_resource_group_list'));
            }
            return $this->sendError(__('responses.not_found_resource_group_list'), 400);
        } catch (\Exception $e) {
            dd($e);
            return $this->sendError(__('responses.send_error'), 500);

        }
    }
}
