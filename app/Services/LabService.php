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
                if (!empty($request->link_url) && !empty($request->social_name)) {
                    foreach ($request->link_url as $key => $value) {
                        if (!empty($request->link_url[$key]) && !empty($request->social_name[$key])) {
                            $social_links=SocialLink::select('id')->where("name",$request->social_name[$key])->first();
                            $social_link_id=$social_links->id;
                            $ExternalLinkUrl=LabExternalLinks::create([
                                'user_id' => auth()->user()->id,
                                'lab_id' => $lab->id,
                                'social_media_link' =>$value,
                                'social_link_id' =>  $social_link_id,
                            ]);
                        }
                    }
                }
                $labskills=$request->skills;
                foreach($labskills as $key=>$skills){
                    $LabSkillsGroupsStack=new LabSkillsGroupsStack;
                    $LabSkillsGroupsStack->lab_id =  $lab->id;
                    $LabSkillsGroupsStack->foreign_id= $skills;
                    $LabSkillsGroupsStack->type= '0';
                    if(!$LabSkillsGroupsStack->save()){
                        DB::rollback();
                        return false;
                    }
                }
                $labtag=$request->tag;
                foreach($labtag as $key=>$tag){
                    $LabTagsGroups=new LabTagsGroups();
                    $LabTagsGroups->lab_id =  $lab->id;
                    $LabTagsGroups->foreign_id= $tag;
                    $LabTagsGroups->type= '0';
                    if(!$LabTagsGroups->save()){
                        DB::rollback();
                        return false;
                    }
                }
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
                            return false;
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