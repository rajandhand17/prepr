<?php
namespace App\Repositories\Api\Master;
use App\Repositories\Api\Master\MasterRepository;
Interface MasterInterface{
    
    public function getCategories(String $categoryName=null);
    public function getSkills(string $skillName=null);
    public function getTags(string $tagName=null);
    public function getIndustry(string $industryName=null);
    public function getTypes(string $typeName=null);
    public function getStages(string $stageName=null);
    public function getverticals(string $verticalsName=null);
    public function getstatus(string $statusName=null);
    public function getmedia(string $mediaName=null);
    
    
}