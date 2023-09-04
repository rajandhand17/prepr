<?php

namespace App\Repositories\Api\Manage\LabProgram;


use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabProgramAchievementsService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabProgramSkillsGroupsStackService;
use App\Services\Manage\LabProgramTagsGroupsService;
use Illuminate\Support\Facades\DB;
class LabProgramRepository implements LabProgramInterface
{
    private $labProgramService;

    private $labProgramAchievementService;

    private $labProgramSkillsGroupsStackService;

    private $labProgramTagsGroupsService;

    private $componentAssociationService;
    public function __construct(LabProgramService $labProgramService, LabProgramAchievementsService $labProgramAchievementService, LabProgramSkillsGroupsStackService $labProgramSkillsGroupsStackService, LabProgramTagsGroupsService $labProgramTagsGroupsService,ComponentAssociationService $componentAssociationService)
    {
        $this->labProgramService = $labProgramService;
        $this->labProgramAchievementService=$labProgramAchievementService;
        $this->labProgramSkillsGroupsStackService=$labProgramSkillsGroupsStackService;
        $this->labProgramTagsGroupsService=$labProgramTagsGroupsService;
        $this->componentAssociationService=$componentAssociationService;
    }

    public function getLabProgramList($request)
    {
        try {
            return $this->labProgramService->getLabProgramList($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabProgramBasedOnSlug($slug)
    {
        try {
            return $this->labProgramService->getLabProgramBasedOnSlug($slug);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function uploadLabProgramMedia($slug)
    {
        try {
            return $this->labProgramService->uploadLabProgramMedia($slug);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function createLabProgram($request, $upload_media,$upload_achievement_image)
    {
        try {
            $createLabProgram=DB::transaction(function () use ($request, $upload_media,$upload_achievement_image) {
                $createdLabProgram = $this->labProgramService->createLabProgram($request, $upload_media);
                $labProgramSkillsGroupsStack = $this->labProgramSkillsGroupsStackService->createLabProgramSkillsGroupsStack($request, $createdLabProgram->id);
                if($request->is_achievement_enabled == 'yes'){
                 $labProgramAchievement = $this->labProgramAchievementService->createLabProgramAchievement($request, $createdLabProgram->id, $upload_achievement_image);
                }
                $labProgramTagsGroupsService = $this->labProgramTagsGroupsService->createLabProgramTagsGroups($request, $createdLabProgram->id);
                $componentAssociation= $this->componentAssociationService->labProgramAssociation($request,$createdLabProgram);
                return [
                        "createLabProgram"=>$createdLabProgram,
                        "labProgramSkillsGroupsStack"=>$labProgramSkillsGroupsStack,
                        "labProgramTagsGroupsService"=>$labProgramTagsGroupsService,
                        "componentAssociation"=>$componentAssociation,
                    ];
            });
            if($createLabProgram['createLabProgram'] && $createLabProgram['labProgramSkillsGroupsStack'] && $createLabProgram['labProgramTagsGroupsService'] && $createLabProgram['componentAssociation']){
                DB::commit();
                 return true;
            }
            DB::rollback();
            return false;
        } catch(\Exception $e){
            DB::rollback();
            dd($e);
            return false;
        }
    }

    public function updateLabProgram($slug,$request, $upload_media,$upload_achievement_image){
        try{
            $createLabProgram=DB::transaction(function () use ($slug,$request, $upload_media,$upload_achievement_image) {
                $updateLabProgram=$this->labProgramService->updateLabProgram($slug,$request, $upload_media);
                $labProgramAchievement = $this->labProgramAchievementService->updateLabProgramAchievement($request, $updateLabProgram->id, $upload_achievement_image);
                $labProgramSkillsGroupsStack = $this->labProgramSkillsGroupsStackService->updateLabProgramSkillsGroupsStack($request, $updateLabProgram->id);
                $labProgramTagsGroupsService = $this->labProgramTagsGroupsService->updateLabProgramTagsGroups($request, $updateLabProgram->id);

                return [
                    "updateLabProgram"=>$updateLabProgram,
                    "labProgramAchievement"=>$labProgramAchievement,
                    "labProgramSkillsGroupsStack"=>$labProgramSkillsGroupsStack,
                    "labProgramTagsGroupsService"=>$labProgramTagsGroupsService,
                ];
            });
            if($createLabProgram['updateLabProgram'] && $createLabProgram['labProgramAchievement'] && $createLabProgram['labProgramSkillsGroupsStack']){
                DB::commit();
                return true;
            }
            DB::rollback();
            return false;
        }catch(\Exception $e){
            return false;
        }
    }

    public function checkSlug($slug){
        try {
            $checkLabProgramSlug=$this->labProgramService->checkSlug($slug);
            return $checkLabProgramSlug;
        }catch(\Exception $e) {
            return false;
        }
    }

    public function delete($slug){
        try {
            return $this->labProgramService->delete($slug);

        }catch(\Exception $e){
            return false;
        }
    }

    public function checkNameExistsOrNot($title){
        try {
           return $this->labProgramService->checkNameExistsOrNot($title);
        }catch(\Exception $e){
            return false;
        }
    }
}
