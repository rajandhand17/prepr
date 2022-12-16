<?php
namespace App\Repositories\Api\Master;
use App\Repositories\Api\Master\MasterRepository;
Interface MasterInterface{
    
    public function getCategories(String $categoryName=null);
    public function getSkills(string $skillName=null);
    public function getTags(string $tagName=null);
    
}