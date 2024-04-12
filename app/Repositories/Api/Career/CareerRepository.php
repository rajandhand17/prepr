<?php

namespace App\Repositories\Api\Career;


class CareerRepository implements CareerInterface
{
    public function __construct(){

    }
    public function getJobsListing(){
        try {
        }catch(\Exception $e){
            return false;
        }
    }
}
