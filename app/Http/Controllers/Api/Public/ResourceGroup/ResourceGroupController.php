<?php

namespace App\Http\Controllers\Api\Public\ResourceGroup;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Repositories\Api\Public\ResourceCollection\ResourceCollectionRepository;
use Illuminate\Http\Request;

class ResourceGroupController extends AppBaseController
{
   private $resourceGroupRepository;

    public function __construct( $resourceGroupRepository)
    {
        $this->resourceGroupRepository = $resourceGroupRepository;
    }

    public static function  index(){
        try{

        }catch( \Exception $e){

            return $this->sendError(__('responses.send_error'), 500);

        }
    }
}
