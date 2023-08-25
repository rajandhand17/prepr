<?php

namespace App\Repositories\Api\Manage\LabProgram;


use DB;

class LabProgramRepository implements LabProgramInterface
{
    private $labProgramService;
    public function __construct(LabProgramService $labProgramService)
    {
        $this->labProgramService=$labProgramService;
    }

    public function createLabProgram($request){
        try{
        return $this->labProgramService->createLabProgram($request);
        }catch(\Exception $e){
            return false;
        }
    }

}
