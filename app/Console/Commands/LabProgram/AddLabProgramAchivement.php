<?php

namespace App\Console\Commands\LabProgram;

use App\Services\Manage\MemberManagementService;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $getData = MemberManagementService::getMembersBasedOnModule(config('constants.member_management_component_type.lab-program'));
            dd(config('constants.module_type.labs'));

            foreach ($getData as $single){
                $user = UserService::getUserByEmail($single->email);
                $lab = LabService::getLabBasedOnId($single->module_id);
                if($user && $lab){
                    $labAchivements = LabAcheivementService::getLabAchivements($lab->id);
                    if($labAchivements){
                        dd($labAchivements);
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
