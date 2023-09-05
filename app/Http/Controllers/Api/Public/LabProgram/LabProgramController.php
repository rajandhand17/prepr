<?php

namespace App\Http\Controllers\Api\Public\LabProgram;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\public\LabProgram\LabProgramResource;
use App\Repositories\Api\Public\LabProgram\LabProgramRepository;
use Illuminate\Http\Request;

class LabProgramController extends AppBaseController
{
    protected $labProgramRepository;

    public function __construct(LabProgramRepository $labProgramRepository)
    {
        $this->labProgramRepository = $labProgramRepository;
    }

    public function index(Request $request)
    {
        try {
            $labProgram = $this->labProgramRepository->getList($request);
            if ($labProgram !== false) {
                $response = [
                    'total_count'  => $labProgram->total(),
                    'per_page'     => $labProgram->perPage(),
                    'count'        => $labProgram->count(),
                    'current_page' => $labProgram->currentPage(),
                    'total_pages'  => $labProgram->lastPage(),
                    'list'         => LabProgramResource::collection($labProgram),
                ];

                return $this->sendResponse($response, __('responses.found_lab_program_list'));
            }

            return $this->sendError(__('responses.not_found_lab_program_list'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show(Request $request, $slug)
    {
        try {
            $labProgram = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
            if ($labProgram) {
                return $this->sendResponse(LabProgramResource::make($labProgram), __('responses.found_lab_program_list'));
            }

            return $this->sendError(__('responses.not_found_lab_program_list'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $labProgram = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
            if ($labProgram !== null) {
                $getColumnNameValue = $this->labProgramRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }
                $checkActivity = $this->labProgramRepository->checkSocialActivity($labProgram->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_lab_program'), 400);
                }
                $labProgram = $this->labProgramRepository->captureSocialActivity($labProgram->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($labProgram) {
                    return $this->sendResponse([], __('responses.'.$action.'_lab_program_successfully'));
                }
            }

            return $this->sendError(__('responses.lab_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function joinLab($slug, Request $request)
    {
        try {
            $labProgram = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
            if ($labProgram !== null) {
                $component = config('constants.lab_component.lab');
                $checkActivity = $this->labProgramRepository->checkJoinedOrNot($labProgram, $component);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_join_lab'), 400);
                }
                $memberList = $this->labProgramRepository->getRecordsFromJoinRequest();
                if (!$memberList && !count($memberList) > 0) {
                    return $this->sendError(__('responses.send_error'), 404);
                }
                $requestedData = $this->labProgramRepository->setJoinRequestParameters($request->language);
                if (!$requestedData) {
                    return $this->sendError(__('responses.send_error'), 403);
                }
                $joinLab = $this->labProgramRepository->joinLab($labProgram, $component, $requestedData, $memberList);
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
