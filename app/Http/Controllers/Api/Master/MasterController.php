<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Master\CategoryResource;
use App\Http\Resources\Master\ProjectTypeResource;
use App\Http\Resources\Master\ProjectStatusResource;
use App\Http\Resources\Master\SkillResource;
use App\Http\Resources\Master\ProjectVerticalsResource;
use App\Http\Resources\Master\SocialLinkResource;
use App\Http\Resources\Master\TagResource;
use App\Http\Resources\Master\ProjectIndustryResource;
use App\Http\Resources\Master\ProjectStageResource;
use Illuminate\Support\Facades\App;
use App\Repositories\Api\Master\MasterRepository;
use Illuminate\Http\Request;

class MasterController extends AppBaseController
{    
      
   public function __construct(MasterRepository $masterRepository)
    {
        $this->masterRepository= $masterRepository;

    }

     /**
     * @OA\Get(
     *     path="/api/v1/master/categories",
     *     tags={"categories"},
     *     summary="Finds lists of categories",
     *     description="Get all the categories lists",
     *     operationId="getCategories",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *    
     *     ),
     * )
     */
    public function getCategories(Request $request)
    {   
        try{
            $category=$this->masterRepository->getcategories($request);
            if($category){
              return $this->sendResponse(CategoryResource::collection($category),__('responses.found_category_list'));
            }
            return $this->sendError(__('responses.not_found_category_list'));
         }catch (\Exception $e){
             return $this->sendError(__('responses.send_error'),500);
         }
    }
  
    /**
     * @OA\Get(
     *     path="/api/v1/master/skills",
     *     tags={"skills"},
     *     summary="Finds lists of skills",
     *     description="Get all the skills lists",
     *     operationId="getSkills",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *    
     *     ),
     * )
     */
    public function getSkills(Request $request)
    {
        try{
            $skills=$this->masterRepository->getSkills($request);
            if($skills){
              return $this->sendResponse(SkillResource::collection($skills),__('responses.found_skill_list'));
          }
          return $this->sendError(__('responses.not_found_skill_list'));
         }catch (\Exception $e){
             return $this->sendError(__('responses.send_error'),500);
         }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/master/tags",
     *     tags={"getTags"},
     *     summary="Finds lists of tags",
     *     description="Get all tags lists",
     *     operationId="getTags",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *    
     *     ),
     * )
     */
    public function getTags(Request $request)
    {      
        try{
            $tag=$this->masterRepository->getTags($request);
            if($tag){
              return $this->sendResponse(TagResource::collection($tag),__('responses.found_tag_list'));
          }
          return $this->sendError(__('responses.not_found_tag_list'));
         }catch (\Exception $e){
             return $this->sendError(__('responses.send_error'),500);
         }
    }
/**
     * @OA\Get(
     *     path="/api/v1/master/industries",
     *     tags={"getProjectIndustries"},
     *     summary="Finds lists of tags",
     *     description="Get all tags lists",
     *     operationId="getProjectIndustries",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *    
     *     ),
     * )
     */
    public function getProjectIndustries(Request $request)
    {
        try{
           $industry=$this->masterRepository->getProjectIndustries($request);
           if($industry){
              return $this->sendResponse(ProjectIndustryResource::collection($industry),__('responses.found_project_industry_list'));
          }
          return $this->sendError(__('responses.not_found_project_industry_list'));
         }
         catch (\Exception $e){
             return $this->sendError(__('responses.send_error'),500);
         }
    }
/**
     * @OA\Get(
     *     path="/api/v1/master/types",
     *     tags={"getProjectTypes"},
     *     summary="Finds lists of tags",
     *     description="Get all tags lists",
     *     operationId="getProjectTypes",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *    
     *     ),
     * )
     */
    public function getProjectTypes(Request $request)
    {
       try{
            $type=$this->masterRepository->getProjectTypes($request);
            if($type){
               return $this->sendResponse(ProjectTypeResource::collection($type),__('responses.found_project_industry_list'));
            }
            return $this->sendError(__('responses.not_found_project_industry_list'));
          }
          catch (\Exception $e){
              return $this->sendError(__('responses.send_error'),500);
          }
    }
/**
     * @OA\Get(
     *     path="/api/v1/master/stages",
     *     tags={"getProjectStages"},
     *     summary="Finds lists of Stages",
     *     description="Get all tags Stages",
     *     operationId="getProjectStages",
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="search values that needed to be considered for filter",
     *         required=false,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *    
     *     ),
     * )
     */
    public function getProjectStages(Request $request)
    {
        try{
           $stages=$this->masterRepository->getStages($request);
           if($stages){
            return $this->sendResponse(ProjectStageResource::collection($stages),__('responses.found_project_stages_list'));
           }
           return $this->sendError(__('responses.not_found_project_stages_list'));
        }
        catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }
    /**
         * @OA\Get(
         *     path="/api/v1/master/verticals",
         *     tags={"getProjectVerticals"},
         *     summary="Finds lists of project verticals",
         *     description="Get all tags project verticals",
         *     operationId="getProjectVerticals",
         *     @OA\Parameter(
         *         name="language",
         *         in="query",
         *         description="language values that needed to be considered for choose languages",
         *         required=true,
         *         explode=true,
         *         
         *     ),
         *     @OA\Parameter(
         *         name="search",
         *         in="query",
         *         description="search values that needed to be considered for filter",
         *         required=false,
         *         explode=true,
         *         
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="successful operation",
         *    
         *     ),
         * )
         */

    public function getProjectVerticals(Request $request)
    {
        try{
            $project_verticals=$this->masterRepository->getVerticals($request);
            if($project_verticals){
             return $this->sendResponse(ProjectVerticalsResource::collection($project_verticals),__('responses.found_project_verticals_list'));
            }
            return $this->sendError(__('responses.not_found_project_verticals_list'));
         }
         catch (\Exception $e){
             return $this->sendError(__('responses.send_error'),500);
         }
    }

    /**
         * @OA\Get(
         *     path="/api/v1/master/status",
         *     tags={"getProjectStatus"},
         *     summary="Finds lists of project status",
         *     description="Get all tags project status",
         *     operationId="getProjectStatus",
         *     @OA\Parameter(
         *         name="language",
         *         in="query",
         *         description="language values that needed to be considered for choose languages",
         *         required=true,
         *         explode=true,
         *         
         *     ),
         *     @OA\Parameter(
         *         name="search",
         *         in="query",
         *         description="search values that needed to be considered for filter",
         *         required=false,
         *         explode=true,
         *         
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="successful operation",
         *    
         *     ),
         * )
         */

    public function getProjectStatus(Request $request)
    {
       try{
          $project_status=$this->masterRepository->getStatus($request);
          if($project_status){
            return $this->sendResponse(ProjectStatusResource::collection($project_status),__('responses.found_project_status_list'));
           }
           return $this->sendError(__('responses.not_found_project_status_list'));
       }
       catch (\Exception $e){
        return $this->sendError(__('responses.send_error'),500);
      }
    }

    /**
         * @OA\Get(
         *     path="/api/v1/master/links",
         *     tags={"getSocialLinks"},
         *     summary="Finds lists of project status",
         *     description="Get all tags project status",
         *     operationId="getSocialLinks",
         *     @OA\Parameter(
         *         name="language",
         *         in="query",
         *         description="language values that needed to be considered for choose languages",
         *         required=true,
         *         explode=true,
         *         
         *     ),
         *     @OA\Parameter(
         *         name="search",
         *         in="query",
         *         description="search values that needed to be considered for filter",
         *         required=false,
         *         explode=true,
         *         
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="successful operation",
         *    
         *     ),
         * )
         */
    public function getSocialLinks(Request $request)
    {
        try{
            $status=$this->masterRepository->getSocialLinks($request);
            if($status){
               return $this->sendResponse(SocialLinkResource::collection($status),__('responses.found_social_links_list'));
             }
             return $this->sendError(__('responses.not_found_social_links_list'));
         }
         catch (\Exception $e){
          return $this->sendError(__('responses.send_error'),500);
        }
    }

}
