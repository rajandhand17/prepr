<?php

namespace App\Http\Controllers\Api\Public\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Lab\LabResource as  PublicLabResource;
use App\Repositories\Api\Public\Lab\LabRepository;
use Illuminate\Http\Request;

class LabController extends AppBaseController
{
    private $labRepository;

    public function __construct(LabRepository $labRepository)
    {
        $this->labRepository = $labRepository;
    }

    public function index(Request $request)
    {
        try {
            $lab = $this->labRepository->getList($request);
            if ($lab) {
                $response = [
                    'total_count'  => $lab->total(),
                    'per_page'     => $lab->perPage(),
                    'count'        => $lab->count(),
                    'current_page' => $lab->currentPage(),
                    'total_pages'  => $lab->lastPage(),
                    'list'         => PublicLabResource::collection($lab),
                ];

                return $this->sendResponse($response, 'responses.labs_fetched_successfully');
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show(Request $request, $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab) {
                return $this->sendResponse(PublicLabResource::make($lab), __('responses.labs_fetched_successfully'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public  function socialActivity($slug, $action){
        try {
            $checkLabExistsOrNot = $this->labRepository->getLabBasedOnSlug($slug);
            if ($checkLabExistsOrNot) {
                $checkLab = $this->labRepository->checkSocialActivity($checkLabExistsOrNot->id,$action);
                $action=str_replace("-","",$action);
                if ($checkLab!==null && isset($checkLab->id) ){

                    return $this->sendError(__('responses.already_'.$action.'_lab'), 400);
                }
                $lab = $this->labRepository->socialActivity($checkLabExistsOrNot->id,$checkLab['column'],$checkLab['action']);
                if ($lab) {
                    return $this->sendResponse([], __('responses.'.$action.'_lab_successfully'));
                }
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
            } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
