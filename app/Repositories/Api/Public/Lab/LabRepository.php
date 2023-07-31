<?php

namespace App\Repositories\Api\Public\Lab;

use App\Repositories\Api\Public\Lab\LabInterface;
use App\Services\Public\LabService;
use Illuminate\Support\Facades\Auth;

class LabRepository implements LabInterface
{
    private $LabService;

    public function getLabList($request){
        try {
            return $this->LabService->getLabList($request);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function getLabBasedOnSlug($slug){

    }
}
