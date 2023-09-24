<?php

namespace App\Http\Controllers\Api\Manage\ResourceModuleDetail;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Repositories\Api\Manage\ResourceModule\ResourceModuleRepository;
use Illuminate\Http\Request;

class ResourceModuleDetailController extends AppBaseController
{
    private $responseModuleDetailsRepository;

    public function __construct( $responseModuleDetailsRepository){

    }
}
