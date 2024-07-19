<?php

namespace App\Http\Controllers\Api\Career;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Career\AddJobPinnedRequest;
use App\Http\Requests\Career\AddJobsRequest;
use App\Http\Requests\Career\AddMultipleJobsRequest;
use App\Http\Resources\Career\AddJobResource;
use App\Http\Resources\Career\CareerResource;
use App\Http\Resources\Career\JobDetailedResource;
use App\Repositories\Api\Career\CareerRepository;
use Illuminate\Http\Request;

class CareerController extends AppBaseController
{
    private $careerRepository;

    public function __construct(CareerRepository $careerRepository)
    {
        $this->careerRepository = $careerRepository;
    }

    public function getMyJobs(Request $request)
    {
        try {
            $getJobs = $this->careerRepository->getMyJobsListing($request);
            if ($getJobs) {
                return $this->sendResponse(
                    CareerResource::collection($getJobs),
                    __('responses.job_listing_successfully')
                );
            }

            return $this->sendResponse([], __('responses.job_listing_successfully'));
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addJobs(AddJobsRequest $request)
    {
        try {
            $checkJobsExistsOrNot = $this->careerRepository->getJobDetails($request->job_id);
            if ($checkJobsExistsOrNot == null) {
                return $this->sendError(__('responses.job_not_exists'));
            }
            $checkJobsExistsOrNotInUserProfile = $this->careerRepository->checkJobsExistsInUsers($request->job_id);
            if ($checkJobsExistsOrNotInUserProfile !== false) {
                return $this->sendResponse($checkJobsExistsOrNot, __('responses.already_added_job'));
            }
            $addedJobs = $this->careerRepository->addJobs($request);
            if ($addedJobs) {
                return $this->sendResponse($addedJobs, __('responses.added_jobs_successfully'));
            }

            return $this->sendResponse([], __('responses.added_jobs_successfully'));
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addMultipleJobs(AddMultipleJobsRequest $request)
    {
        try {
            $checkJobsExistsOrNot = $this->careerRepository->getJobsDetails($request->job_ids);
            if ($checkJobsExistsOrNot == null) {
                return $this->sendError(__('responses.job_not_exists'));
            }
            $addedJobs = $this->careerRepository->addMultipleJobs($request);
            if ($addedJobs) {
                return $this->sendResponse($addedJobs, __('responses.added_jobs_successfully'));
            }

            return $this->sendResponse([], __('responses.added_jobs_successfully'));
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function jobPinned(AddJobPinnedRequest $request)
    {
        try {
            $addedPinnedJobs = $this->careerRepository->addJobPinned($request);
            if ($addedPinnedJobs) {
                $message = $request->pinned == 'yes' ? __('responses.pinned_jobs_successfully') : __('responses.pinned_jobs_successfully_removed');

                return $this->sendResponse(AddJobResource::make($addedPinnedJobs), $message);
            }

            return $this->sendError(__('responses.pinned_job_failed'), 400);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteJob($jobId)
    {
        try {
            $checkJobExistsOrNot = $this->careerRepository->checkJobExistsOrNot($jobId);
            if (!$checkJobExistsOrNot) {
                return $this->sendError(__('responses.job_not_exists'), 404);
            }
            $deleteJob = $this->careerRepository->deleteJob($jobId);
            if ($deleteJob) {
                return  $this->sendResponse([], __('responses.delete_job_successfully'));
            }
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getRelatedCareer(Request $request)
    {
        try {
            $relatedCareer = $this->careerRepository->getRelatedCareer($request);
            if ($relatedCareer) {
                $response = [
                    'total_count'  => $relatedCareer->total(),
                    'per_page'     => $relatedCareer->perPage(),
                    'count'        => $relatedCareer->count(),
                    'current_page' => $relatedCareer->currentPage(),
                    'total_pages'  => $relatedCareer->lastPage(),
                    'list'         => CareerResource::collection($relatedCareer),
                ];

                return $this->sendResponse($response, __('responses.related_career_successfully'));
            }

            return $this->sendResponse([], __('responses.related_career_successfully'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getJobDetailed($id)
    {
        try {
            $jobDetailed = $this->careerRepository->getJobDetails($id);
            if ($jobDetailed) {
                return $this->sendResponse(JobDetailedResource::make($jobDetailed), __('responses.get_job_details'));
            }

            return $this->sendError(__('responses.job_not_exists'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
