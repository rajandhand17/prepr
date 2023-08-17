<?php

namespace App\Http\Controllers\Api\Public\Lab;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Lab\LabResource;
use App\Repositories\Api\Public\Lab\LabRepository;
use Illuminate\Http\Request;
use stdClass;

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

            if ($lab !== false) {
                $response = [
                    'total_count'  => $lab->total(),
                    'per_page'     => $lab->perPage(),
                    'count'        => $lab->count(),
                    'current_page' => $lab->currentPage(),
                    'total_pages'  => $lab->lastPage(),
                    'list'         => LabResource::collection($lab),
                ];

                return $this->sendResponse($response, __('responses.found_labs_list'));
            }

            return $this->sendError(__('responses.not_found_labs_list'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show(Request $request, $slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);

            if ($lab) {
                return $this->sendResponse(LabResource::make($lab), __('responses.found_labs_list'));
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab !== null) {
                $getColumnNameValue = $this->labRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }
                $checkActivity = $this->labRepository->checkSocialActivity($lab->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_lab'), 400);
                }
                $lab = $this->labRepository->captureSocialActivity($lab->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($lab) {
                    return $this->sendResponse([], __('responses.'.$action.'_lab_successfully'));
                }
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function joinLab($slug,Request $request)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab !== null) {
                $component = config('constants.lab_component.lab');
                $checkActivity = $this->labRepository->checkJoinedOrNot($lab, $component);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_join_lab'), 400);
                }
                $memberList = $this->labRepository->getRecordsFromJoinRequest();
                if (!$memberList && !count($memberList) > 0) {
                    return $this->sendError(__('responses.send_error'), 404);
                }
                $requestedData = $this->labRepository->setJoinRequestParameters($request->language);
                if (!$requestedData) {
                    return $this->sendError(__('responses.send_error'), 403);
                }
                $joinLab = $this->labRepository->joinLab($lab, $component, $requestedData, $memberList);
                if ($joinLab) {
                    return $this->sendResponse([], __('responses.join_lab_successfully'));
                }

                return $this->sendError(__('responses.join_lab_failed'), 400);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function unJoinLab($slug)
    {
        try {
            $lab = $this->labRepository->getLabBasedOnSlug($slug);
            if ($lab !== null) {
                $component = config('constants.lab_component.lab');
                $checkActivity = $this->labRepository->checkJoinedOrNot($lab, $component);
                if ($checkActivity === false) {
                    return $this->sendError(__('responses.already_un_join_lab'), 400);
                }
                $data = new stdClass();
                $data->email = [auth()->user()->email];
                $joinLab = $this->labRepository->unJoinLab($lab, $component, $data);
                if ($joinLab) {
                    return $this->sendResponse([], __('responses.un_join_lab_successfully'));
                }

                return $this->sendError(__('responses.join_lab_failed'), 400);
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
