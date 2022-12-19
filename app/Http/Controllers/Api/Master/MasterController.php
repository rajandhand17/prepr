<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Master\CategoryResource;
use App\Http\Resources\Master\ProjectTypeResource;
use App\Http\Resources\Master\SkillResource;
use App\Http\Resources\Master\TagResource;
use App\Http\Resources\Master\IndustryResource;
use Illuminate\Support\Facades\App;
use App\Repositories\Api\Master\MasterRepository;
use Illuminate\Http\Request;

class MasterController extends AppBaseController
{  
    public function __construct(MasterRepository $masterrepository,Request $request)
    {
        $this->masterrepository= $masterrepository;
        App::setlocale($request->lang);
    }

    public function getCategories(Request $request)
    {   
        try{
            $category=$this->masterrepository->getcategories($request->search);
            if($category){
              return $this->sendResponse(CategoryResource::collection($category),__('responses.category_list'));
          }
         }catch (\Exception $e){
             return $this->sendError(__('responses.send_error'));
         }
    }

    public function getSkills(Request $request)
    {  
        try{
            $category=$this->masterrepository->getSkills($request->search);
            if($category){
              return $this->sendResponse(SkillResource::collection($category),__('responses.skill_list'));
          }
         }catch (\Exception $e){
             return $this->sendError(__('responses.send_error'));
         }
    }

    public function getTags(Request $request)
    {
        try{
            $tag=$this->masterrepository->getTags($request->search);
            if($tag){
              return $this->sendResponse(TagResource::collection($tag),__('responses.tag_list'));
          }
         }
         catch (\Exception $e){
             return $this->sendError(__('responses.send_error'));
         }
    }

    public function getIndustries(Request $request)
    {      
        try{
           $industry=$this->masterrepository->getIndustry($request->search);
           if($industry){
              return $this->sendResponse(IndustryResource::collection($industry),__('responses.project_industry_list'));
          }
         }
         catch (\Exception $e){
             return $this->sendError(__('responses.send_error'));
         }
    }

    public function getTypes(Request $request)
    {    
       try{
            $industry=$this->masterrepository->getTypes($request->search);
            if($industry){
               return $this->sendResponse(ProjectTypeResource::collection($industry),__('responses.project_type'));
            }
          }
          catch (\Exception $e){
              return $this->sendError(__('responses.send_error'));
          }
    }

}
