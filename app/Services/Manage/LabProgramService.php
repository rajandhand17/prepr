<?php

namespace App\Services\Manage;

use App\Events\Labs\DeleteLabAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\LabProgram;
use HiFolks\RandoPhp\Randomize;

class LabService{

    public function createLabProgram($request){
        $labProgram=new LabProgram();
        $labProgram->language =$request->language;
        $labProgram->title =$request->title;
        $labProgram->description =$request->description;
        $labProgram->lab_id =$request->lab_id;
        $labProgram->user_id =$request->user_id;
        $labProgram->media =$request->media;
        $labProgram->privacy =$request->privacy;
        $labProgram->status =$request->status;
        $labProgram->is_auto_create =$request->is_auto_create;
        $labProgram->prize =$request->prize;
        $labProgram->points =$request->points;
        $labProgram->trophy =$request->trophy;
        $labProgram->save();
        return $labProgram;
    }
}
