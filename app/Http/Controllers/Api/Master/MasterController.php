<?php

namespace App\Http\Controllers\Api\Master;

use App\Repositories\Api\Master\MasterInterface;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Master\CategoryResource;
use App\Http\Resources\Master\SkillResource;
use App\Http\Resources\Master\TagResource;
use Illuminate\Support\Facades\App;

class MasterController extends AppBaseController
{  
    public function __construct(MasterInterface $masterInterface)
    {
        $this->masterInterface= $masterInterface;
    }

    public function getCategories($lang,$categoryName=null)
    {
        try{
            App::setlocale($lang);
            $category=$this->masterInterface->getcategories($categoryName);
            if($category){
              return $this->sendResponse(CategoryResource::collection($category),__('messages.responses.category_list'));
          }
         }
         catch (\Exception $e){
             return $this->sendError(__('messages.responses.send_error'));
         }
    }

    public function getSkills($lang,$skillName=null)
    {  
        try{
            App::setlocale($lang);
            $category=$this->masterInterface->getSkills($skillName);
            if($category){
              return $this->sendResponse(SkillResource::collection($category),__('messages.responses.skill_list'));
          }
         }
         catch (\Exception $e){
             return $this->sendError(__('messages.responses.send_error'));
         }
       
    }

    public function getTags($lang,$tagName=null)
    {
        try{
            App::setlocale($lang);
            $tag=$this->masterInterface->getTags($tagName);
            if($tag){
              return $this->sendResponse(TagResource::collection($tag),__('messages.responses.tag_list'));
          }
         }
         catch (\Exception $e){
             return $this->sendError(__('messages.responses.send_error'));
         }
    }
    
}
