<?php

namespace App\Console\Commands\Lab;

use App\Helpers\UtilityHelper;
use App\Services\AchievementConditionListService;
use App\Services\AchievementService;
use App\Services\Manage\ChallengePathService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabAcheivementService;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceModuleService;
use App\Services\ModuleCompletionStatusService;
use App\Services\ProjectService;
use App\Services\Public\LabService;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddLabAchivement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'achievement:add-lab-achievement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to assigned users lab achievement';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $getLabJoinedMembers = MemberManagementService::getMembersBasedOnModule(config('constants.member_management_component_type.lab'));
            foreach ($getLabJoinedMembers as $labMember){
                $fetchUserData = UserService::getUserByEmail($labMember->email);
                $fetchLab = LabService::getLabBasedOnId($labMember->module_id);
                if($fetchUserData && $fetchLab){
                    $moduleType = '0';
                    $getLabCompletion = ModuleCompletionStatusService::fetchModuleIdBasedProgress($fetchLab->id, $moduleType, $fetchUserData->id);
                    $labAchievements = LabAcheivementService::getLabAchivements($fetchLab->id);
                    if($labAchievements){
                        $addAchievement = 0;
                        foreach ($labAchievements->achievement_condition as $achievementCondition){
                            $achievementConditionData = AchievementConditionListService::getAchievementConditionByID($fetchLab->language, $achievementCondition);
                            if ($achievementConditionData->title  == 'Complete All Challenges') {
                                $getAssociationChallengeIds = ComponentAssociationService::fetchChallengeIdsAssociatedLabId($fetchLab->id);
                                $getChallegeIds = ChallengeService::getChallengeIdBasedOnId($getAssociationChallengeIds);
                                if($getChallegeIds->isNotEmpty()){
                                    $checkProjectCreated = ProjectService::fetchCompletedChallenges($getChallegeIds, $fetchUserData);
                                    if($getChallegeIds == $checkProjectCreated){
                                        $addAchievement++;
                                    }
                                }
                            } elseif ($achievementConditionData->title == 'Complete All Challenge Paths') {
                                $getAssociatedChallengePathIds = ComponentAssociationService::fetchChallengePathIdsAssociatedLabId($fetchLab->id);
                                $getChallengePathIds = ChallengePathService::getChallengePathIdBasedOnIds($getAssociatedChallengePathIds);
                                if($getChallengePathIds->isNotEmpty()){
                                    $getPathCompletion = ModuleCompletionStatusService::fetchChallengePathCompletedBasedOnIds($getChallengePathIds, $fetchUserData->id);
                                    if($getPathCompletion->count() == count($getChallengePathIds)){
                                        $addAchievement++;
                                    }
                                }

                            } elseif ($achievementConditionData->title == 'Complete All Resource Modules') {
                                $getAssociatedResourceModuleIds = ComponentAssociationService::fetchResourceModuleIdsAssociatedLabId($fetchLab->id);
                                $getResourceModuleIds = ResourceModuleService::getResourceModuleGetBasedId($getAssociatedResourceModuleIds);
                                if($getResourceModuleIds){
                                    $getModuleCompletion = ModuleCompletionStatusService::fetchResourceModuleCompletedBasedOnIds($getResourceModuleIds, $fetchUserData->id);
                                    if($getModuleCompletion->count() == count($getResourceModuleIds)){
                                        $addAchievement++;
                                    }
                                }
                            } elseif ($achievementConditionData->title == 'Complete All Resource Collections') {
                                $getAssociatedResourceCollectionIds = ComponentAssociationService::fetchResourceCollectionIdsAssociatedLabId($fetchLab->id);
                                $getResourceCollectionIds = ResourceCollectionService::getResourceCollectionGetBasedId($getAssociatedResourceCollectionIds);
                                if ($getResourceCollectionIds) {
                                    $getCollectionCompletion = ModuleCompletionStatusService::fetchResourceCollectionCompletedBasedOnIds($getResourceCollectionIds, $fetchUserData->id);
                                    if ($getCollectionCompletion->count() == count($getResourceCollectionIds)) {
                                        $addAchievement++;
                                    }
                                }
                            } elseif ($achievementConditionData->title == 'Complete All Resource Groups') {
                                $getAssociatedResourceGroupIds = ComponentAssociationService::fetchResourceGroupIdsAssociatedLabId($fetchLab->id);
                                $getResourceGroupIds = ResourceGroupService::getResourceGroupBasedOnIdArray($getAssociatedResourceGroupIds);
                                if ($getResourceGroupIds) {
                                    $getGroupCompletion = ModuleCompletionStatusService::fetchResourceGroupCompletedBasedOnIds($getResourceGroupIds, $fetchUserData->id);
                                    if ($getGroupCompletion->count() == count($getResourceGroupIds)) {
                                        $addAchievement++;
                                    }
                                }
                            } elseif ($achievementConditionData->title == 'Complete All') {
                                if ($getLabCompletion && $getLabCompletion->percentage == '100') {
                                    $addAchievement++;
                                }
                            }
                        }

                        if(count($labAchievements->achievement_condition) != $addAchievement){
                            if ($getLabCompletion->is_completed == '0') {
                                $getLabCompletion->is_completed = '1';
                                $getLabCompletion->save();
                                AchievementService::addLabAchievement($fetchLab->id, $fetchUserData->id);
                            }
                        }

                    }
                }
            }
        }
        catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error('Adding lab achievement failed');
        }

    }
}
