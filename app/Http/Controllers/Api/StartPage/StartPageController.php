<?php

namespace App\Http\Controllers\Api\StartPage;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\StartPage\LabResource;
use App\Http\Resources\StartPage\PartnerCompanyResource;
use App\Http\Resources\StartPage\SkillResource;
use App\Http\Resources\StartPage\TestimonialsResource;
use App\Repositories\Api\StartPage\StartPageRepository;

class StartPageController extends AppBaseController
{
    private $startPageRepository;

    public function __construct(StartPageRepository $startPageRepository)
    {
        $this->startPageRepository = $startPageRepository;
    }

    public function index()
    {
        try {
            $startPage = $this->startPageRepository->index();
            if ($startPage) {
                $startPage = [
                    'labs'         => LabResource::collection($startPage['labs']),
                    'skills'       => SkillResource::collection($startPage['skills']),
                    'partners'     => PartnerCompanyResource::collection($startPage['partners']),
                    'testimonials' => TestimonialsResource::collection($startPage['testimonials']),
                ];

                return  $this->sendResponse($startPage, __('responses.front_page_success'));
            }

            return $this->sendError(__('responses.front_page_failed'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
