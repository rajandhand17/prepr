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
use DB;
class LabService
{
    public  function store($request,$upload_cover_image,$upload_acheivements_image){
        try {
        DB::beginTransaction();
        $model=new Lab();
        $lab=new Lab();
        $lab->language=$request->language;
        $lab->user_id=auth()->user()->id;
        $lab->organization_id=(int)$request->organization;
        $lab->media=$upload_cover_image;
        $lab->title=$request->title;
        $lab->description=$request->description;
        $lab->category_id=$request->category_id;
        $lab->slug=UtilityHelper::generateSlug($request->title, $model);
        switch($request->status){
            case "draft":
                $status=0;
            break;
            case "publish":
                $status=1;
            break;
            case "archive":
                $status=2;
            break;
            default:
            $status = 0;
            break;
        }
        $lab->status=$status;
        $lab->privacy=$request->privacy;
        $lab->total_share=0;
        $lab->uuid=Randomize::chars(10)->alphanumeric()->unique()->generate();        ;
        $lab->is_auto_created="1";
        if($lab->save()){
            $labaddress=new LabAddress();
            $labaddress->lab_id =$lab->id;
            $labaddress->latitute=$request->latitute;
            $labaddress->longitude=$request->longitude;
            $labaddress->address=$request->address;
            $labaddress->city=$request->city;
            $labaddress->country=$request->country;
            if($labaddress->save()){
                switch ($request->achievement_en_switch) {
                    case 'yes':
                        $labAchievement=new LabAcheivement();
                        $labAchievement->lab_id=$lab->id;
                        $labAchievement->achievement_name=$request->achievement_name;
                        $labAchievement->achievement_points=$request->achievement_points;
                        $labAchievement->achievement_condition=$request->achievement_condition;
                        $labAchievement->achievement_image=$upload_acheivements_image;
                        if(!$labAchievement->save()){
                            DB::rollback();
                        }
                        break;
                    default:
                        $module_type = null;
                        break;
                }
                switch ($request->associated_challenge_switch){
                    case 'yes':
                        $selectedChallenge=$request->challenge_id;
                        foreach ($selectedChallenge as $key => $challenge_id) {
                        $labchllebges= LabChallenges::create([
                                'lab_id' =>  $lab->id,
                                'challenge_id' => $challenge_id,
                                'sequence_no' => $key+1,
                            ]);
                        }
                    break;
                    default:
                        $module_type = null;
                        break;
                }
                switch ($request->associated_resource_switch){
                    case 'yes':
                        $SelectedPaths=$request->challenge_path_id;
                        foreach ($SelectedPaths as $key => $path_id) {
                            LabChallenges::create([
                                'lab_id' =>  $lab->id,
                                'challenge_path_id' => $path_id,
                                'sequence_no' => $key+1,
                            ]);
                        }
                    break;
                    default:
                        $module_type = null;
                        break;
                }
                DB::commit();
                return $lab;
            }
        }
        DB::rollback();
        return false;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return false;
        }
    }

    public function uploadImage($image,$type){
        try {
            $uploadLabCoverImage = FileUploadHelper::uploadImageToS3($image,$type);
            if ($uploadLabCoverImage == false) {
                return false;
            }

            return $uploadLabCoverImage;
        } catch (\Exception $e) {
            return false;
        }
    }
}