<?php

namespace App\Http\Controllers\Api\Career;

use App\Http\Controllers\Controller;
use App\Repositories\Api\Career\CareerRepository;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    private $careerRepository;
    public function __construct(CareerRepository $careerRepository){
        $this->careerRepository=$careerRepository;
    }

    public function getJobs(){
        try {
            $getJobs=$this->careerRepository->getJobsListing();
            if($getJobs){
                return $this->response($getJobs,__('response.job_listing_successfully'));
            }
            return $this->response([],__('response.job_listing_successfully'));
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
