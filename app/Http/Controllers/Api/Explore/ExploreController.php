<?php

namespace App\Http\Controllers\Api\Explore;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Profile\ProfileResource;
use App\Repositories\Api\Explore\ExploreRepository;
use Illuminate\Http\Request;

class ExploreController extends AppBaseController
{
    private $exploreRepository;
    public function __construct(ExploreRepository $exploreRepository){
        $this->exploreRepository=$exploreRepository;
    }

    public function index(Request $request){
        try {
            $explore = $this->exploreRepository->index($request);
            if($explore){
                dd($request);
                return $this->sendResponse($explore, __('responses.found_user_profile_detail'));
            }
            return $this->sendError(__('responses.send_error'),404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);

        }
    }

}
