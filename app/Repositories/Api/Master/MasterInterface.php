<?php
namespace App\Repositories\Api\Master;
Interface MasterInterface{

    public function getCategories($request);
    public function getSkills($request);
    public function getTags($request);
    public function getProjectIndustries($request);
    public function getProjectTypes($request);
    public function getStages($request);
    public function getVerticals($request); 
    public function getStatus($request);
    public function getSocialLinks($request);
    public function getSkillGroups($request);


}
