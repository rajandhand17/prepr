<?php

namespace App\Http\Controllers\Api\Manage\Resource;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Resource\CreateResourceRequest;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleRepository;
use Illuminate\Http\Request;

class ResourceController extends AppBaseController
{
    private $resourceModuleRepository;

    function __construct(ResourceModuleRepository $resourceModuleRepository){
        $this->resourceModuleRepository = $resourceModuleRepository;
    }

    public function index(){
        try{
        }catch(\Exception $e){
            return $this->sendError(__('response.send_error'));
        }
    }
    public function create(Request $request){
        try{
            $createResource=$this->resourceModuleRepository->createResourceModule($request);
        }catch(\Exception $e){
            return $this->sendError(__('response.send_error'));
        }
    }

}
