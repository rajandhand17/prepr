<?php

namespace App\Http\Controllers\Api\Manage\LabProgram;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\LabProgram\LabProgramRepository;
use Illuminate\Http\Request;

class LabProgramController extends AppBaseController
{
    private $labProgramRepository;
    public function __construct(LabProgramRepository $labProgramRepository){
        $this->labProgramRepository=$labProgramRepository;
    }
    public function create($request){
        try{
            $createLabProgram=$this->labProgramRepository->createLabProgram($request);
            if($createLabProgram){
                return true;
            }
            return false;
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
