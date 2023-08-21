<?php

namespace App\Repositories\Api\Manage\LabProgram;

use App\Services\Manage\LabProgramService;
use DB;

class LabProgramRepository implements LabProgramInterface
{
    private $labProgramService;

    public function __construct(LabProgramService $labProgramService)
    {
        $this->labProgramService = $labProgramService;
    }
    public function createLabProgram($request, $upload_cover_image)
    {
        try{
        return $this->labProgramService->createLabProgram($request, $upload_cover_image);
        } catch (\Exception $e) {
            return false;
        }
    }

}
