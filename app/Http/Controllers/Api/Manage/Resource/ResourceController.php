<?php

namespace App\Http\Controllers\Api\Manage\Resource;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Resource\CreateResourceRequest;
use App\Repositories\Api\Manage\Resource\ResourceRepository;
use Illuminate\Http\Request;

class ResourceController extends AppBaseController
{
    private $resourceRepository;

    function __construct(ResourceRepository $resourceRepository){
        $this->resourceRepository = $resourceRepository;
    }

    public function create(Request $request){
        try{
            $createResource=$this->resourceRepository->store($request);
        }catch(\Exception $e){
            return $this->sendError(__('response.send_error'));
        }
    }

}
