<?php
namespace App\Repositories\Api\Master;
use App\Repositories\Api\Master\MasterRepository;
Interface MasterInterface{

    public function getCategories($request);
    public function getSkills($request);
    public function getTags(string $tagName=null);
    public function getIndustry($request);
    public function getTypes($request);
    public function getStages($request);
    public function getVerticals($request);
    public function getStatus($request);
    public function getmedia(string $mediaName=null);


}
