<?php

namespace App\Http\Controllers\Api\Manage\Report;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\LabProgram\LabProgramRepository;
use App\Repositories\Api\Manage\Report\Lab\LabReportRepository;
use Symfony\Component\HttpFoundation\Response;

class LabProgramReportController extends AppBaseController
{
    public function __construct(
        protected LabReportRepository $labReportRepository,
        protected LabProgramRepository $labProgramRepository
    ) {
    }

    public function labProgramMemberProgress(string $slug)
    {
        try {
            $challenge = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);

            if ($challenge) {
                $memberProgress = $this->labReportRepository->getLabProgramMemberProgress($challenge);

                if ($memberProgress !== false) {
                    return $this->sendResponse($memberProgress, __('Lab Program member progress'));
                }

                return $this->sendError(__('responses.failed_to_fetch_lab_program_member_progress'), Response::HTTP_BAD_REQUEST);
            }

            return $this->sendError(__('responses.not_found_lab_program_view'), Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.failed_to_fetch_lab_program_member_progress'), Response::HTTP_BAD_REQUEST);
        }
    }
}
