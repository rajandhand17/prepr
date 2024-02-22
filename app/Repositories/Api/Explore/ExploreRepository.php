<?php

namespace App\Repositories\Api\Explore;


use App\Models\Lab;
use App\Models\User;
use App\Services\Public\LabService;

class ExploreRepository implements ExploreInterface
{
    private $labService;

    public function __construct(LabService $labService)
    {
        $this->labService =$labService;
    }

    public function index($request){
        try {
            return $this->labService->getList($request);
        }catch (\Exception $e) {
            return false;
        }
    }

}
