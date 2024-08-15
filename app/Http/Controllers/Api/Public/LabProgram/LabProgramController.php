<?php

namespace App\Http\Controllers\Api\Public\LabProgram;

use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\LabProgram\LabProgramResource;
use App\Repositories\Api\Public\LabProgram\LabProgramRepository;
use App\Services\LastVisitedActivityModuleService;
use Illuminate\Http\Request;
use stdClass;

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
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $labProgram = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
            if ($labProgram) {
                if ($labProgram->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_program_not_accessible'), 403);
                }
                if (auth('api')->check()) {
                    // For user progress tracking
                    $userId = auth('api')->user()->id;
                    TrackUserProgressHelper::trackLabProgramUserProgress($labProgram, $userId);

                    // For last visited activity tracking
                    $moduleType = config('constants.module_type.lab_programs');
                    LastVisitedActivityModuleService::lastVisitedActivityModule($labProgram->id, $userId, $moduleType);
                }

                return $this->sendResponse(LabProgramResource::make($labProgram), __('responses.found_lab_program_view'));
            }

            return $this->sendError(__('responses.not_found_lab_program_view'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $labProgram = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
            if ($labProgram !== null) {
                if ($labProgram->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_program_not_accessible'), 403);
                }
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

            return $this->sendError(__('responses.lab_program_slug_not_found'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function joinLabProgram($slug, Request $request)
    {
        try {
            $labProgram = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
            if ($labProgram !== null) {
                if ($labProgram->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_program_not_accessible'), 403);
                }
                $component = config('constants.member_management_component_type.lab_program');
                $moduleType = config('constants.member_management_component_type.lab_program');
                $checkActivity = $this->labProgramRepository->checkJoinedOrNot($labProgram, $moduleType);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_join_lab_program'), 400);
                }
                $memberList = $this->labProgramRepository->getRecordsFromJoinRequest();
                if (!$memberList && !count($memberList) > 0) {
                    return $this->sendError(__('responses.send_error'), 404);
                }
                $requestedData = $this->labProgramRepository->setJoinRequestParameters($request->language);
                if (!$requestedData) {
                    return $this->sendError(__('responses.send_error'), 403);
                }
                $joinLabProgram = $this->labProgramRepository->joinLabProgram($labProgram, $component, $requestedData, $memberList);
                if ($joinLabProgram) {
                    return $this->sendResponse([], __('responses.join_lab_program_successfully'));
                }

                return $this->sendError(__('responses.join_lab_program_failed'), 400);
            }

            return $this->sendError(__('responses.lab_program_slug_not_found'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function unJoinLabProgram($slug)
    {
        try {
            $labProgram = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
            if ($labProgram !== null) {
                if ($labProgram->is_accessible == '0') {
                    return $this->sendError(__('responses.lab_not_accessible'), 403);
                }
                $component = config('constants.member_management_component_type.lab_program');
                $moduleType = config('constants.member_management_component_type.lab_program');
                $checkActivity = $this->labProgramRepository->checkJoinedOrNot($labProgram, $moduleType);
                if ($checkActivity === false) {
                    return $this->sendError(__('responses.already_un_join_lab_program'), 400);
                }
                $data = new stdClass();
                $data->email = [auth()->user()->email];
                $joinLab = $this->labProgramRepository->unJoinLabProgram($labProgram, $component, $data);
                if ($joinLab) {
                    return $this->sendResponse([], __('responses.un_join_lab_program_successfully'));
                }

                return $this->sendError(__('responses.join_lab_program_failed'), 400);
            }

            return $this->sendError(__('responses.lab_program_slug_not_found'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
