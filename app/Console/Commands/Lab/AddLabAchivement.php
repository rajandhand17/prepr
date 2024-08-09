<?php

namespace App\Console\Commands\Lab;

use App\Helpers\UtilityHelper;
use App\Services\AchievementConditionListService;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabAcheivementService;
use App\Services\Manage\MemberManagementService;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $getData = MemberManagementService::getMembersBasedOnModule(config('constants.member_management_component_type.lab'));

            foreach ($getData as $single){
                $user = UserService::getUserByEmail($single->email);
                $lab = LabService::getLabBasedOnId($single->module_id);
                if($user && $lab){
                    $labAchievements = LabAcheivementService::getLabAchivements($lab->id);
                    if($labAchievements){
                        $addAchievement = 0;
                        foreach ($labAchievements->achievement as $achievement){
                            $condition = AchievementConditionListService::getAchievementConditionByID($achievement);
                            if ($condition->condition_title == 'Complete All Challenges') {

                                $getChallegeIds = ComponentAssociationService::fetchChallengeIdsAssociatedLabId($lab->id);
                                if($getChallegeIds){
                                    $checkProjectCreated = ProjectService::fetchCompletedChallenges($getChallegeIds, $user);
                                    if($getChallegeIds == $checkProjectCreated){
                                        $addAchievement++;
                                    }
                                }
                            } elseif ($condition->condition_title == 'Complete All Challenge Paths') {

                                $getChallengePathIds = ComponentAssociationService::fetchChallengePathIdsAssociatedLabId($lab->id);
                                if($getChallengePathIds){
                                    $getModuleCompletion = ModuleCompletionStatusService::checkChallengePathAchievementAssignedOrNotArray($getChallengePathIds,$user->id);
                                    if($getModuleCompletion->count() == count($getChallengePathIds)){
                                        $addAchievement++;
                                    }
                                }

                            } elseif ($condition->condition_title == 'Complete All Resource Modules') {

                                $getResourceModuleIds = ComponentAssociationService::checkResourceModuleProgressBasedOnResourceIds($lab->id);
                                if($getResourceModuleIds){
                                    $getModuleCompletion = ModuleCompletionStatusService::checkChallengePathAchievementAssignedOrNotArray($getResourceModuleIds,$user->id);
                                    if($getModuleCompletion->count() == count($getResourceModuleIds)){
                                        $addAchievement++;
                                    }
                                }
                            } elseif ($condition->condition_title == 'Complete All Resource Collections') {

                            } elseif ($condition->condition_title == 'Complete All Resource Groups') {

                            } elseif ($condition->condition_title == 'Complete All') {

                            }
                        }

                        if(count($labAchievements) == $addAchievement){

                        }

                    }
                }
            }
        }
        catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error('Allow Challenge Winner selection status not updated');
        }

    }
}
