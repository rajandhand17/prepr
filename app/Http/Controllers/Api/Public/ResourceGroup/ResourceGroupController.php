<?php

namespace App\Http\Controllers\Api\Public\ResourceGroup;

use App\Http\Controllers\AppBaseController;
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
            $responseGroup = $this->resourceGroupRepository->getResourceGroupList($request);
            if ($responseGroup) {
                $response = [
                    'total_count'  => $responseGroup->total(),
                    'per_page'     => $responseGroup->perPage(),
                    'count'        => $responseGroup->count(),
                    'current_page' => $responseGroup->currentPage(),
                    'total_pages'  => $responseGroup->lastPage(),
                    'list'         => ResourceGroupResource::collection($responseGroup),
                ];

                return $this->sendResponse($response, __('responses.found_resource_group_list'));
            }

            return $this->sendError(__('responses.not_found_resource_group_list'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
