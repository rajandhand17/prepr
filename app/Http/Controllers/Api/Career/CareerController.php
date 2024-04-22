<?php

namespace App\Http\Controllers\Api\Career;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Career\careerResource;
use App\Http\Resources\Career\JobDetailedResource;
use App\Repositories\Api\Career\CareerRepository;
use App\Services\UserJobTitlesService;
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
                return $this->sendResponse(careerResource::collection($getJobs),__('response.job_listing_successfully'));
            }
            return $this->sendResponse([],__('response.job_listing_successfully'));
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getJob($id){
        try {
            $getJobsList=$this->careerRepository->getJob($id);
            if($getJobsList){
                return $this->sendResponse(careerResource::make($getJobsList),__('response.job_listing_successfully'));
            }
            return $this->sendResponse([],__('response.job_listing_successfully'));
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function addJobs(Request $request){
        try {
            $checkJobsExistsOrNot=UserJobTitlesService::checkJobsExistsInUsers($request->job_title_id);
            if($checkJobsExistsOrNot!==false){
                return $this->sendResponse($checkJobsExistsOrNot,__('responses.already_added_job'));
            }
            $addedJobs=$this->careerRepository->addJobs($request);
            if($addedJobs){
                return $this->sendResponse($addedJobs,__('responses.added_jobs_successfully'));
            }
            return $this->sendResponse([],__('responses.added_jobs_successfully'));
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public  function addJobPinned($jobId){
        try {
            $addedJobs=$this->careerRepository->addJobPinned($jobId);
            if($addedJobs){
                return $this->sendResponse($addedJobs,__('responses.added_jobs_successfully'));
            }
            return $this->sendResponse([],__('responses.added_jobs_successfully'));
        }catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function deleteJob($jobId){
        try {
            $deleteJob=$this->careerRepository->deleteJob($jobId);
            if($deleteJob){
                return  $this->sendResponse([],__('responses.send_error'));
            }
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function getRelatedCareer(){
        try {
            $relatedCareer=$this->careerRepository->getRelatedCareer();
            if($relatedCareer){
                return $this->sendResponse(careerResource::collection($relatedCareer),__("responses.get_related_career"));
            }
            return $this->sendResponse([],__('responses.get_related_career'));
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function getJobDetailed($id){
        try {
            $jobDetailed=$this->careerRepository->getJobDetails($id);
            if($jobDetailed){
                return $this->sendResponse(JobDetailedResource::make($jobDetailed),__('responses.get_job_details'));
            }
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }
}
