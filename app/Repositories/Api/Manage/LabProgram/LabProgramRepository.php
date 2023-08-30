<?php

namespace App\Repositories\Api\Manage\LabProgram;

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
    public function __construct(LabProgramService $labProgramService, LabProgramAchievementsService $labProgramAchievementService, LabProgramSkillsGroupsStackService $labProgramSkillsGroupsStackService, LabProgramTagsGroupsService $labProgramTagsGroupsService)
    {
        $this->labProgramService = $labProgramService;
        $this->labProgramAchievementService=$labProgramAchievementService;
        $this->labProgramSkillsGroupsStackService=$labProgramSkillsGroupsStackService;
        $this->labProgramTagsGroupsService=$labProgramTagsGroupsService;
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
                $labProgramAchievement = $this->labProgramAchievementService->createLabProgramAchievement($request, $createdLabProgram->id, $upload_achievement_image);
                $labProgramSkillsGroupsStack = $this->labProgramSkillsGroupsStackService->createLabProgramSkillsGroupsStack($request, $createdLabProgram->id);
                $labProgramTagsGroupsService = $this->labProgramTagsGroupsService->createLabProgramTagsGroups($request, $createdLabProgram->id);
                    return [
                        "createLabProgram"=>$createdLabProgram,
                        "labProgramAchievement"=>$labProgramAchievement,
                        "labProgramSkillsGroupsStack"=>$labProgramSkillsGroupsStack,
                        "labProgramTagsGroupsService"=>$labProgramTagsGroupsService,
                    ];
            });
            if($createLabProgram['createLabProgram'] && $createLabProgram['labProgramAchievement'] && $createLabProgram['labProgramSkillsGroupsStack'] && $createLabProgram['labProgramTagsGroupsService']){
                DB::commit();
                 return true;
            }
            DB::rollback();
            return false;
        } catch(\Exception $e) {
            DB::rollback();
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
