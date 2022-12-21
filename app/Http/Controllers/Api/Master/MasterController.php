<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Master\CategoryResource;
use App\Http\Resources\Master\ProjectTypeResource;
use App\Http\Resources\Master\ProjectStatusResource;
use App\Http\Resources\Master\SkillResource;
use App\Http\Resources\Master\VerticalsResource;
use App\Http\Resources\Master\MediaResource;
use App\Http\Resources\Master\TagResource;
use App\Http\Resources\Master\IndustryResource;
use App\Http\Resources\Master\ProjectStageResource;
use Illuminate\Support\Facades\App;
use App\Repositories\Api\Master\MasterRepository;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

class MasterController extends AppBaseController
{

    public function __construct(MasterRepository $masterRepository)
    {
        $this->masterRepository= $masterRepository;

    }

    public function getCategories(Request $request)
    {
        try{
            $category=$this->masterRepository->getcategories($request);
            if($category){
              return $this->sendResponse(CategoryResource::collection($category),__('responses.category_list'));
            }
            return $this->sendError(__('responses.not_found_category_list'));
         }catch (\Exception $e){
             return $this->sendError(__('responses.send_error'),500);
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

    public function getstages(Request $request)
    {
        try{
           $stages=$this->masterrepository->getStages($request->search);
           if($stages){
            return $this->sendResponse(ProjectStageResource::collection($stages),__('responses.project_stages'));
           }
        }
        catch (\Exception $e){
            return $this->sendError(__('responses.send_error'));
        }
    }

    public function getverticals(Request $request)
    {
        try{
            $stages=$this->masterrepository->getverticals($request->search);
            if($stages){
             return $this->sendResponse(VerticalsResource::collection($stages),__('responses.project_stages'));
            }
         }
         catch (\Exception $e){
             return $this->sendError(__('responses.send_error'));
         }
    }

    public function getstatus(Request $request)
    {
       try{
          $status=$this->masterrepository->getstatus($request->search);
          if($status){
            return $this->sendResponse(ProjectStatusResource::collection($status),__('responses.project_status'));
           }
       }
       catch (\Exception $e){
        return $this->sendError(__('responses.send_error'));
      }
    }

    public function getmedia(Request $request)
    {
        try{
            $status=$this->masterrepository->getmedia($request->search);
            if($status){
               return $this->sendResponse(MediaResource::collection($status),__('responses.social_media'));
             }
         }
         catch (\Exception $e){
          return $this->sendError(__('responses.send_error'));
        }
    }

}
