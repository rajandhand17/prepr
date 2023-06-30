<?php

namespace App\Services;

use App\Helpers\FileUploadHelper;
use App\Models\Lab;
use Predis\Command\Redis\AUTH;
use HiFolks\RandoPhp\Randomize;
use App\Helpers\UtilityHelper;
use App\Models\LabAcheivement;
use App\Models\LabAddress;
use App\Models\labChallenges;
use App\Models\LabExternalLinks;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabTagsGroups;
use App\Models\SocialLink;
use DB;
class LabService
{
    public  function store($request,$upload_cover_image){
        try {
        switch($request->status){
            case "draft":
                $status=config('constants.lab_status.draft');
            break;
            case "publish":
                $status=config('constants.lab_status.publish');
            break;
            case "archive":
                $status=config('constants.lab_status.archive');
            break;
            default:
            $status=config('constants.lab_status.draft');
            break;
        }
        switch($request->privacy){
            case "yes":
                $privacy=config('constants.lab_privacy.yes');
            break;
            case "no":
                $privacy=config('constants.lab_privacy.no');
            break;
            default:
            $privacy=config('constants.lab_privacy.yes');
            break;
        }
        $model=new Lab();
        $createSlug=UtilityHelper::generateSlug($request->title, $model);
        $slug=self::generateUniqueSlug($createSlug);
        DB::beginTransaction();
        $model=new Lab();
        $lab=new Lab();
        $lab->language=$request->language;
        $lab->user_id=auth()->user()->id;
        $lab->organization_id=(int)$request->organization_id;
        $lab->media=$upload_cover_image;
        $lab->title=$request->title;
        $lab->description=$request->description;
        $lab->category_id=$request->category_id;
        $lab->slug=$slug;
        $lab->status=$status;
        $lab->privacy=$privacy;
        $lab->total_share=0;
        $lab->uuid=Randomize::chars(10)->alphanumeric()->unique()->generate();        ;
        $lab->is_auto_created="1";
        if($lab->save()){
            DB::commit();
            return $lab;
        }
        DB::rollback();
        return false;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function uploadCoverImage($image){
        try {
            $uploadLabCoverImage = FileUploadHelper::uploadImageToS3($image,"lab");
            if ($uploadLabCoverImage == false) {
                return false;
            }
            return $uploadLabCoverImage;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function checkSlugExistsOrNot($slug)
    {
        try {
            $slug = Lab::where('slug', $slug)->first();
            if ($slug) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateUniqueSlug($slug) {
        $newSlug = $slug;
        $counter = 1;
        while (self::checkSlugExistsOrNot($newSlug)) {
            $newSlug = $slug . '-' . $counter;
            $counter++;
        }
        return $newSlug;
    }

    public function getLabList($request){
        try {
            $labList=Lab::with('organization')->with('user')->with('category');
            $labList = $this->filterLabList($labList,$request);
            if (!$labList->isEmpty()) {
                $labList->transform(function ($item) {
                    if ($item['status'] == 0) {
                        $item['status'] = 'draft';
                    }
                    if ($item['status'] == 1) {
                        $item['status'] = 'published';
                    }
                    if ($item['status'] == 2) {
                        $item['status'] = 'deactivated';
                    }
                    if ($item['privacy'] == 0) {
                        $item['privacy'] = 'yes';
                    }
                    if ($item['privacy'] == 1) {
                        $item['privacy'] = 'no';
                    }
                    return $item;
                });

                return $labList;
            }
            return $labList;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filterLabList($labList,$request){
        try {
            if(isset($request->privacy) && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'yes':
                        $privacy = config('constants.lab_privacy.yes');
                        break;
                    case 'no':
                        $privacy = config('constants.lab_privacy.no');
                        break;
                    default:
                        $privacy = config('constants.lab_privacy.no');
                }
                $labList=$labList->where("privacy",$privacy);
            }
            $labList=$labList->get();
            return $labList;
        } catch (\Exception $e){
            return false;
        }
    }
}