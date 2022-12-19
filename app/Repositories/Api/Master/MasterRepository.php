<?php
namespace App\Repositories\Api\Master;
use App\Repositories\Api\Master\MasterInterface;
use App\Models\Category;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\Industrie;
use App\Models\ProjectType;
use App\Models\ProjectStage;
use Dotenv\Util\Str;

class MasterRepository implements MasterInterface{
     
    private $category;
    private $skill;
    private $tag;
    function __construct(Category $category,Skill $skill,Tag $tag,Industrie $industry, ProjectType $projecttype) {
        $this->category = $category;
        $this->skill = $skill;
        $this->tag=$tag;
        $this->industry=$industry;
        $this->projecttype=$projecttype;
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
}

