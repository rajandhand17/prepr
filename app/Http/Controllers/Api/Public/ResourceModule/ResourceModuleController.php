<?php

namespace App\Http\Controllers\Api\Public\ResourceModule;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\ResourceModule\ResourceModuleResource;
use App\Repositories\Api\Public\ResourceModule\ResourceModuleRepository;
use Illuminate\Http\Request;

class ResourceModuleController extends AppBaseController
{
    private $resourceModuleRepository;

    public function __construct(ResourceModuleRepository $resourceModuleRepository)
    {
        $this->resourceModuleRepository = $resourceModuleRepository;
    }

    public function index(Request $request)
    {
        try {
            $responseModuleList = $this->resourceModuleRepository->getResourceModuleList($request);
            if ($responseModuleList) {
                $response = [
                    'total_count'  => $responseModuleList->total(),
                    'per_page'     => $responseModuleList->perPage(),
                    'count'        => $responseModuleList->count(),
                    'current_page' => $responseModuleList->currentPage(),
                    'total_pages'  => $responseModuleList->lastPage(),
                    'list'         => ResourceModuleResource::collection($responseModuleList),
                ];

                return $this->sendResponse($response, __('responses.found_resource_module_list'));
            }
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
