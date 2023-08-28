<?php

namespace App\Repositories\Api\Manage\LabProgram;

use App\Services\Manage\LabProgramService;

class LabProgramRepository implements LabProgramInterface
{
    private $labProgramService;

    public function __construct(LabProgramService $labProgramService)
    {
        $this->labProgramService = $labProgramService;
    }

    public function getLabProgramList($request)
    {
        try {
            return $this->labProgramService->getLabProgramList($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabProgramBasedOnSlug($request)
    {
        try {
            return $this->labProgramService->getLabProgramBasedOnSlug($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function uploadLabProgramMedia($slug)
    {
        try {
            return $this->labProgramService->uploadLabProgramMedia($slug);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function createLabProgram($request, $upload_media)
    {
        try {
            return $this->labProgramService->createLabProgram($request, $upload_media);
        } catch(\Exception $e) {
            return false;
        }
    }
}
