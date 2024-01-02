<?php

namespace App\Console\Commands\OldDataMigration;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class UserPersonal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-personal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will migrate all users personal data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users personal table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_personals')->chunkById(1000, function ($userPersonals, $key) {
                foreach ($userPersonals as $key=> $userPersonalDetail) {
                    $checkUsers = \App\Models\User::find($userPersonalDetail->user_id);
                    if ($checkUsers == null) {
                        continue;
                    }

                    $checkUserPersonalDetails=\App\Models\UserPersonal::where('id',$userPersonalDetail->id)->first();
                    if($checkUserPersonalDetails){
                        $userPersonal=$checkUserPersonalDetails;
                    }else{
                        $userPersonal=new \App\Models\UserPersonal();
                    }
                    switch ($userPersonalDetail->status){
                        case 'looking_team':
                            $status='0';
                            break;
                        case 'currently_mentor':
                            $status='1';
                            break;
                        case 'looking_employers':
                            $status='2';
                            break;
                        case 'currently_team':
                            $status='3';
                            break;
                        case 'Looking_teammates':
                            $status='4';
                            break;
                        case 'looking_employees':
                            $status='5';
                            break;
                        case 'looking_invest':
                            $status='6';
                            break;
                        case 'looking_mentor':
                            $status='7';
                            break;
                        case 'looking_for_investors':
                            $status='8';
                            break;
                        case 'looking_to_create_social_impact':
                            $status='9';
                            break;
                        case 'looking_to_learn':
                            $status='10';
                            break;
                        case 'looking_to_solve_problems':
                            $status='11';
                            break;
                        case 'looking_to_build_skills':
                            $status='12';
                            break;
                        default:
                            $status='1';
                            break;
                    }
                    $user_type=null;
                    switch ($userPersonalDetail->user_type) {
                        case 'employee':
                            $user_type ='0';
                            break;
                        case 'investor':
                            $user_type = '1';
                            break;
                        case 'teacher':
                            $user_type = '2';
                            break;
                        case 'job_seeker':
                            $user_type = '3';
                            break;
                        case 'student':
                            $user_type = '4';
                            break;
                        case 'recent_grad':
                            $user_type = '5';
                            break;
                        case 'expert':
                            $user_type = '6';
                            break;
                        case 'employer':
                            $user_type = '7';
                            break;
                        case 'Recent Grad':
                            $user_type = '8';
                            break;
                        case 'facilitator':
                            $user_type = '9';
                            break;
                        case 'Job Seeker':
                            $user_type = '10';
                            break;
                        case 'startup':
                            $user_type = '11';
                            break;
                        case 'learner':
                            $user_type = '12';
                            break;
                        case 'mentor':
                            $user_type ='13';
                            break;
                        case 'innovator':
                            $user_type = '14';
                            break;
                        case 'aspiring_entrepreneur':
                            $user_type = '15';
                            break;
                        case 'evaluator':
                            $user_type = '16';
                            break;
                        case 'small_mid_size_business':
                            $user_type = '17';
                            break;
                        case 'intrapreneur':
                            $user_type = '18';
                            break;
                        case 'ngo_charity_not_for_profit':
                            $user_type = '19';
                            break;
                        case 'enterprise':
                            $user_type = '20';
                            break;
                        case 'applicant':
                            $user_type ='21';
                            break;
                        case 'educational_institution':
                            $user_type = '22';
                            break;
                        case 'community_organization':
                            $user_type ='23';
                            break;
                        default:
                            $user_type = null;
                            break;
                    }
                    switch ($userPersonal->gender){
                        case 'male':
                            $gender='0';
                            break;
                        case 'female':
                            $gender='1';
                            break;
                        case 'other':
                            $gender='2';
                            break;
                        case 'decline':
                            $gender='3';
                            break;
                        default:
                            $gender='3';
                            break;
                    }

                    $userPersonal->user_id=$userPersonalDetail->user_id;
                    $userPersonal->about=$userPersonalDetail->about?$userPersonalDetail->about:null;
                    $userPersonal->gender=$gender;
                    $userPersonal->date_of_birth=$userPersonalDetail->date_of_birth?$userPersonalDetail->date_of_birth:null;
                    $userPersonal->age=$userPersonalDetail->age;
                    $userPersonal->purpose=$status;
                    $userPersonal->user_type=$user_type;
                    $userPersonal->recent_immigrant=$userPersonalDetail->recent_immigrant=='1'?'2':'1';
                    $userPersonal->indigenous_group=$userPersonalDetail->indigenous_group=='1'?'2':'1';
                    $userPersonal->visible_minority=$userPersonalDetail->visible_minority=='1'?'2':'1';
                    $userPersonal->disability=$userPersonalDetail->disability=='1'?'2':'1';
                    $userPersonal->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users personal table completed.');
        } catch(\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
