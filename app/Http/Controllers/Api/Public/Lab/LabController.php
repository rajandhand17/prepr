<?php

namespace App\Http\Controllers\Api\Public\Lab;

use App\Http\Controllers\AppBaseController;
use Exception;
use Illuminate\Http\Request;
use App\Repositories\Api\Public\LabRepository;
class LabController extends AppBaseController
{
    private $labRepository;

    public function __construct(LabRepository $labRepository)
    {
        $this->labRepository = $labRepository;
    }

    public function index(Request $request){
        try {
            $organization = $this->labRepository->getLabList($request);
            if($organization){

            }
        }catch (\Exception $e){

        }
    }
}
