<?php

namespace App\Http\Controllers\Api\Career;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Career\CareerRepository;
use Illuminate\Http\Request;

class CareerController extends AppBaseController
{
    private $careerRepository;
    public function __construct(CareerRepository $careerRepository){
        $this->careerRepository=$careerRepository;
    }

    public function getMyJobs(Request $request){
        try {
            $getJobs=$this->careerRepository->getMyJobsListing($request);
            if($getJobs){
                return $this->sendResponse($getJobs,__('response.job_listing_successfully'));
            }
            return $this->sendResponse([],__('response.job_listing_successfully'));
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
