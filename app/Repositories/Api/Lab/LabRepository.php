<?php
namespace App\Repositories\Api\Lab;

use App\Models\Lab;

class LabRepository extends LabInterface{
   private $lab;
   function __construct(Lab $lab)
   {
       $this->lab=$lab;
   }

    public function list($request)
    {
        try{
            return $this->lab->list($request);
         }
         catch (\Exception $e){

            return false;
         }
    }

    public function create($request)
    {
        try {
            return $this->lab->create($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function draft($request)
    {
        try {
            return $this->lab->draft($request);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function edit($request)
    {
        try {
            return $this->lab->edit($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($request)
    {
        try {
            return $this->lab->delete($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function labDetail($request)
    {
        try {
            return $this->lab->labDetail($request);
        } catch (\Exception $e) {
           return false;
        }
    }

    public function checkLabSlug($request)
    {
        try {
          return $this->lab->checkLabSlug($request);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkLabName($request)
    {
        try {
            return $this->lab->checkLabName($request);

        } catch (\Exception $e) {
           return false;
        }
    }
     public function getSkills($request)
     {
        try{
            return $this->lab->getSkills($request);
        }catch (\Exception $e){
            return false;
        }
     }

     public function getTags($request)
     {
        try {
            return $this->lab->getTags($request);
        } catch (\Exception $e) {
            return false;
        }
     }
     
     public function getLabPrograms($request)
     {
        try {
            return $this->lab->getLabPrograms($request);
        } catch (\Exception $e) {
            return false;
        }
     }
     public function genrateReportExcel($request)
     {
        try {
            return $this->lab->genrateReportExcel($request);
        } catch (\Exception $e) {
            return false;
        }
     }
     public function genrateReportPdf($request)
     {
        try {
            return $this->lab->genrateReportPdf($request);
        } catch (\Exception $e) {
            return false;
        }
     }
     public function likeUnlike($request)
     {
        try {
            return $this->lab->likeUnlike($request);
        } catch (\Exception $e) {
            return false;
        }
     }

     public function followUnfollow($request)
     {
        try {
            return $this->lab->followUnfollow($request);
        } catch (\Exception $e) {
            return false;
        }
     }
     public function joinUnjoin($request)
     {
        try {
            return $this->lab->joinUnjoin($request);
        } catch (\Exception $e) {
            return false;
        }
     }
     public function share($request)
     {
        try {
            return $this->lab->share($request);
        } catch (\Exception $e) {
            return false;
        }
     }
     public function view($request)
     {
        try {
            return $this->lab->view($request);
        } catch (\Exception $e) {
            return false;
        }
     }
}