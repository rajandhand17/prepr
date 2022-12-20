<?php
namespace App\Repositories\Api\Master;
use App\Repositories\Api\Master\MasterInterface;
use App\Models\Category;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\Industrie;
use App\Models\ProjectType;
use App\Models\ProjectStage;
use App\Models\Vertical;
use App\Models\ProjectStatus;
use App\Models\SocialMedia;

class MasterRepository implements MasterInterface{
     
    private $category;
    private $skill;
    private $tag;
    private $industry;
    private $projecttype;
    private $projectstage;
    private $vertical;
    private $projectstatus;
    private $socialmedia;
    function __construct(Category $category,Skill $skill,Tag $tag,Industrie $industry, ProjectType $projecttype, ProjectStage $projectstage, Vertical $vertical, ProjectStatus $projectstatus, SocialMedia $socialmedia) {
        $this->category = $category;
        $this->skill = $skill;
        $this->tag=$tag;
        $this->industry=$industry;
        $this->projecttype=$projecttype;
        $this->projectstage=$projectstage;
        $this->vertical=$vertical;
        $this->projectstatus=$projectstatus;
        $this->socialmedia=$socialmedia;
    }

    public function getCategories(String $categoryName=null)
    {   
        try{ 
           return $this->category->getAll($categoryName);
        }
        catch (\Exception $e){
            return false;
        }
    }

    public function getSkills(string $skillName=null){
        
        try{
            return $this->skill->getAll($skillName);
        }
        catch (\Exception $e){
            return false;
        }
    }

    public function getTags(String $tagName=null)
    {
        try{
            return $this->tag->getAll($tagName);
        }
        catch (\Exception $e){
            return false;
        }
    }

    public function getIndustry(string $industryName=null){
          
        try{ 
            return $this->industry->getAll($industryName);
        }
        catch (\Exception $e){
            return false;
        }
    }

    public function getTypes(string $typename=null)
    {   
       try{ 
           return $this->projecttype->getAll($typename);
        }
        catch (\Exception $e){
            return false;
        }
    }

    public function getStages(string $stagename = null)
    {
        try{
           return $this->projectstage->getAll($stagename);
        }
        catch(\Exception $e){
            return false;
        }
    }

    public function getverticals(string $verticalsName = null)
    {
       try{
          return $this->vertical->getAll($verticalsName);
       }
       catch(\Exception $e){
        return false;
       }
    }

    public function getstatus(string $StatusName = null)
    {   
        try{
             return $this->projectstatus->getAll($StatusName);
         }
         catch(\Exception $e){
          return false;
         }
    }

    public function getmedia(string $mediaName=null)
    {
        try{
            return $this->socialmedia->getAll($mediaName);
        }
        catch(\Exception $e){
         return false;
        }
    }
}

