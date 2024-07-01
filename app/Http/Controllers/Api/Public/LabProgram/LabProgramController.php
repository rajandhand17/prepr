<?php

namespace App\Http\Controllers\Api\Public\LabProgram;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\LabProgram\LabProgramResource;
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

                return $this->sendResponse(LabProgramResource::make($labProgram), __('responses.found_lab_program_list'));
            }

            return $this->sendError(__('responses.not_found_lab_program_list'), 404);
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
}
