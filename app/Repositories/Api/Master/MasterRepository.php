<?php
namespace App\Repositories\Api\Master;

use App\Models\Category;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\ProjectIndustry;
use App\Models\ProjectType;
use App\Models\ProjectStage;
use App\Models\ProjectVertical;
use App\Models\ProjectStatus;
use App\Models\SocialLink;
use App\Models\SkillGroup;

class MasterRepository implements MasterInterface{

    private $category;
    private $skill;
    private $tag;
    private $project_industry;
    private $project_type;
    private $project_stage;
    private $project_verticals;
    private $project_status;
    private $social_link;
    private $skill_group;

    function __construct(Category $category,Skill $skill,Tag $tag,ProjectIndustry $project_industry, ProjectType $project_type, ProjectStage $project_stage, ProjectVertical $project_verticals, ProjectStatus $project_status, SocialLink $social_link, SkillGroup $skill_group) {
        $this->category = $category;
        $this->skill = $skill;
        $this->tag=$tag;
        $this->project_industry=$project_industry;
        $this->project_type=$project_type;
        $this->project_stage=$project_stage;
        $this->project_verticals=$project_verticals;
        $this->project_status=$project_status;
        $this->social_link=$social_link;
        $this->skill_group=$skill_group;
    }

    public function getCategories($request)
    {    
        try{
           return $this->category->getCategories($request->language,$request->search,$request->component);
        }
        catch (\Exception $e){
           return false;
        }
    }

    public function getSkills($request)
    {    
       try{
           return $this->skill->getSkills($request->language,$request->search);
        }
        catch (\Exception $e){
           return false;
        }
    }

    public function getTags($request)
    {
        try{
            return $this->tag->getTags($request->language,$request->search);
        }
        catch (\Exception $e){
            return false;
        }
    }
 
    public function getProjectIndustries($request){

        try{
            return $this->project_industry->getProjectIndustries($request->language,$request->search);
        }
        catch (\Exception $e){
            return false;
        }
    }

    public function getProjectTypes($request)
    {
       try{
           return $this->project_type->getProjectTypes($request->language,$request->search);
        }
        catch (\Exception $e){
            return false;
        }
    }

    public function getStages($request)
    {
        try{
           return $this->project_stage->getProjectStages($request->language,$request->search);
        }
        catch(\Exception $e){
            return false;
        }
    }

    public function getVerticals($request)
    {
       try{
          return $this->project_verticals->getProjectVerticals($request->language,$request->search);
       }
       catch(\Exception $e){
        return false;
       }
    }

    public function getStatus($request)
    {
        try{
             return $this->project_status->getProjectStatus($request->language,$request->search);
         }
         catch(\Exception $e){
          return false;
         }
    }

    public function getSocialLinks($request)
    {
        try{
            return $this->social_link->getSocialLinks($request->language,$request->search);
        }
        catch(\Exception $e){
         return false;
        }
    }
    
    public function getSkillGroups($request)
    {   
        try{
            return $this->skill_group->getSkillGroups($request->language,$request->search,$request->skill_stacks,$request->skills);
        }catch(\Exception $e){
             return false;
        }
    }
}

