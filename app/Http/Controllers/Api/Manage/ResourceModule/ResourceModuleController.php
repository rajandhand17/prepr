<?php

namespace App\Http\Controllers\Api\Manage\ResourceModule;

use App\Http\Controllers\Controller;
use App\Http\Resources\Manage\LabProgram\LabProgramResource;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleRepository;
use Illuminate\Http\Request;

class ResourceModuleController extends Controller
{
    private $resourceModuleRepository;

    public function __construct(ResourceModuleRepository $resourceModuleRepository){
        $this->resourceModuleRepository = $resourceModuleRepository;
    }

    public function index(Request $request){
        try{
            $listResourceModule=$this->resourceModuleRepository->index($request);
            if ($listResourceModule) {
                $response = [
                    'total_count'  => $listResourceModule->total(),
                    'per_page'     => $listResourceModule->perPage(),
                    'count'        => $listResourceModule->count(),
                    'current_page' => $listResourceModule->currentPage(),
                    'total_pages'  => $listResourceModule->lastPage(),
                    'list'         => $listResourceModule,
                ];

                return $this->sendResponse($response, __('responses.found_lab_program_list'));
            }

        }catch(\Exception $e){
            return $this->sendError(__('response.send_error'));
        }
    }
}
