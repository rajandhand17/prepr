<?php

namespace App\Http\Controllers\Api\Lab;

use App\Repositories\Api\Lab\LabInterface;
use App\Repositories\Api\Lab\LabRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Lab\LabResource;
use Illuminate\Http\Request;

class LabController extends AppBaseController{
    private $labRepository;
   public function __construct(LabRepository $labRepository)
   {
      $this->labRepository=$labRepository;
   }

   public function index(Request $request)
   {   
    try {
        $list=$this->labRepository->list($request);
        if($list){
           return $this->sendResponse(LabResource::collection($list), __('responses.lab'));
        }
        return false;
        } catch (\Exception $e) {
        return false;
        }
   }

   public function view(Request $request)
   {  
       dd("Ds");
   }

   public function create($request)
   {
    try {
      $create=$this->labRepository->create($request);
      if($create){
            return true;
      }
      return false;
    }catch (\Exception $e) {
        return false;
    }
   }

   public function draft($request)
   {
        try {
            $create=$this->labRepository->draft($request);
            if($create){
                  return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
   }

   public function edit($request)
   {
        try {
            $edit=$this->labRepository->edit($request);
            if($edit){
                  return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
   }

   public function delete(Request $request)
   {
    try {
        $delete=$this->labRepository->delete($request);
        if($delete){
            return $delete;
        }
            return false;
    } catch (\Exception $e) {
        return false;
    }
   }

   public function labDetail($request)
   {
    try {
        $lab_detail=$this->labRepository->labDetail($request);
        if($lab_detail){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }

   public function checkLabSlug($request)
   {
    try {
        $check_lab_slug=$this->labRepository->checkLabSlug($request);
        if($check_lab_slug){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }

   public function checkLabName($request)
   {
    try {
        $check_lab_name=$this->labRepository->checkLabName($request);
        if($check_lab_name){
              return true;
        }
        return false;
    } catch (\Exception $e) {
       return false;
    }
   }

   public function getSkills($request)
   {
    try {
        $skills=$this->labRepository->getSkills($request);
        if($skills){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }
   public function getTags($request)
   {
    try {
        $tags=$this->labRepository->getTags($request);
        if($tags){
              return true;
        }
        return false;
    } catch (\Exception $e) {
       return false;
    }
   }
   public function getLabPrograms($request)
   {
    try {
        $get_lab_programs=$this->labRepository->getLabPrograms($request);
        if($get_lab_programs){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }
   public function genrateReportExcel($request)
   {
    try {
        $genrate_report_excel=$this->labRepository->genrateReportExcel($request);
        if($genrate_report_excel){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }

   public function genrateReportPdf($request)
   {
    try {
        $genrate_report_pdf=$this->labRepository->genrateReportPdf($request);
        if($genrate_report_pdf){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }

   public function likeUnlike($request)
   {
    try {
        $like_unlike=$this->labRepository->likeUnlike($request);
        if($like_unlike){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }
   public function followUnfollow($request)
   {
    try {
        $follow_unfollow=$this->labRepository->followUnfollow($request);
        if($follow_unfollow){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }
   public function joinUnjoin($request)
   {
    try {
        $join_unjoin=$this->labRepository->joinUnjoin($request);
        if($join_unjoin){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }

   public function share($request)
   {
    try {
        $share=$this->labRepository->share($request);
        if($share){
              return true;
        }
        return false;
    } catch (\Exception $e) {
        return false;
    }
   }
  
}