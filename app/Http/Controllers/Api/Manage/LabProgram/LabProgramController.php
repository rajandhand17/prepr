<?php

namespace App\Http\Controllers\Api\Manage\LabProgram;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\LabProgram\CreateLabProgramRequest;
use App\Repositories\Api\Manage\LabProgram\LabProgramRepository;
use Illuminate\Http\Request;

class LabProgramController extends AppBaseController
{
    private $labProgramRepository;

    public function __construct(LabProgramRepository $labProgramRepository)
    {
        $this->labProgramRepository = $labProgramRepository;
    }

    public function index(Request $request)
    {
        try {
            $listLabProgram = $this->labProgramRepository->getLabProgramList($request);

            return $listLabProgram;
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $view = $this->labProgramRepository->getLabProgramBasedOnSlug($slug);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateLabProgramRequest $request)
    {
        try {
            $upload_media = config('site-settings.default_lab_program_profile_image');
            if ($request->media !== null) {
                $uploaded_media = $this->labProgramRepository->uploadLabProgramMedia($request->media);
                if (!$uploaded_media) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_media = $uploaded_media;
            }
            $createLabProgram = $this->labProgramRepository->createLabProgram($request, $upload_media);
            if ($createLabProgram) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
