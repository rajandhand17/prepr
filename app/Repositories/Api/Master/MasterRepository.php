<?php
namespace App\Repositories\Api\Master;
use App\Repositories\Api\Master\MasterInterface;
use App\Models\Category;
use App\Models\Skill;
use App\Models\Tag;

class MasterRepository implements MasterInterface{
     
    private $category;
    private $skill;
    private $tag;
    function __construct(Category $category,Skill $skill,Tag $tag) {
        $this->category = $category;
        $this->skill = $skill;
        $this->tag=$tag;
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
}

