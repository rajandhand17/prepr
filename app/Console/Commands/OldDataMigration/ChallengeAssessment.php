<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ChallengeAssessment as ChallengeAssessments;

class ChallengeAssessment extends Command
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:challenge-assessment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old challenge assessment table data to new db structure.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            $this->info('Migrating old data for challenge assessment table.');
            DB::beginTransaction();

            $challange_assessments = DB::connection('mysql2')->table('challange_assessments')->get();

            if($challange_assessments->count() > 0){
                foreach ($challange_assessments as $key => $single_challange_assessments){
                    $challange_assessments_details=[
                        'challenge_id' => $single_challange_assessments->challenge_id,
                        'assessment_type' => $single_challange_assessments->assessment_type,
                        'visibility' => $single_challange_assessments->visibility,
                        'members' => $single_challange_assessments->members,
                        'guidline' => $single_challange_assessments->guidline,
                        'attachment' => $single_challange_assessments->attachment,
                    ];
                    $check_challange_assessments = ChallengeAssessments::where($challange_assessments_details)->first();
                    if(!$check_challange_assessments){
                        ChallengeAssessments::create($challange_assessments_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for challange assessments table completed.');
                return;
            }
            DB::rollback();
            $this->error('No challange assessments found.');

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}