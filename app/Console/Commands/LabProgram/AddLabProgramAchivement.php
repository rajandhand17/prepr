<?php

namespace App\Console\Commands\LabProgram;

use App\Helpers\UtilityHelper;
use App\Services\AchievementService;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Manage\LabProgramAchievementsService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\MemberManagementService;
use App\Services\ModuleCompletionStatusService;
use App\Services\UserService;
use Exception;
use Illuminate\Console\Command;

class AddLabProgramAchivement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'achievement:add-lab-program-achievement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to assigned users lab program achievement';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $getLabProgramJoinedMembers = MemberManagementService::getMembersBasedOnModule(config('constants.member_management_component_type.lab_program'));
            foreach ($getLabProgramJoinedMembers as $labProgramMember){
                $fetchUserData = UserService::getUserByEmail($labProgramMember->email);
                $fetchLabProgram = LabProgramService::getLabProgramBasedOnId($labProgramMember->module_id);
                if($fetchUserData && $fetchLabProgram){
                    $labProgramAchievement = LabProgramAchievementsService::getLabProgramsAchivements($fetchLabProgram->id);
                    if ($labProgramAchievement) {
                        $getAssociationLabIds = ComponentAssociationService::fetchLabIdsAssociatedLabProgramId($fetchLabProgram->id);
                        $getLabIds = LabService::getLabIdBasedOnId($getAssociationLabIds);
                        if ($getLabIds->isNotEmpty()) {
                            $getLabCompletion = ModuleCompletionStatusService::fetchLabCompletedBasedOnIds($getLabIds, $fetchUserData->id);
                            if ($getLabCompletion->count() == count($getLabIds)) {
                                $moduleType = '1';
                                $getLabCompletion = ModuleCompletionStatusService::fetchModuleIdBasedProgress($fetchLabProgram->id, $moduleType, $fetchUserData->id);
                                if ($getLabCompletion && $getLabCompletion->is_completed == '0') {
                                    $getLabCompletion->is_completed = '1';
                                    $getLabCompletion->save();
                                    AchievementService::addLabProgramAchievement($fetchLabProgram->id, $fetchUserData->id);
                                }
                            }
                        }
                    }
                }
            }
        }
        catch (Exception $e) {
            UtilityHelper::logError($e);
            $this->error('Adding lab program achievement failed');
        }
    }
}
