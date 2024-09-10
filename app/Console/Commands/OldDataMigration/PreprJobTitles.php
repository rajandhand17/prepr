<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\JobTitle;
use App\Models\JobTitleSkill;
use App\Models\RelatedJobTitle;
use App\Models\UserJobTitle;
use Carbon\Carbon;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PreprJobTitles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:jobs-titles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old jobs\' tables data to new db structure.';

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
            $this->info('Migrating old data for table (titles).');
            DB::beginTransaction();

            DB::connection('mysql2')->table('titles')->orderBy('id')->chunkById(1000, function ($jobTitles) use (&$insertArr) {
                $insertArr = [];

                foreach ($jobTitles as $job) {
                    $uuid = Randomize::chars(10)->alphanumeric()->generate();
                    $jobs_details = [
                        'id'                => $job->id,
                        'uuid'              => $uuid,
                        'title'             => $job->name,
                        'fr_CA_title'       => $job->fr_CA_name,
                        'lightcast_id'      => $job->lc_id,
                        'pathway_id'        => null,
                        'created_at'        => $job->created_at,
                        'updated_at'        => $job->updated_at,
                    ];

                    $insertArr[] = $jobs_details;
                }

                if (!empty($insertArr)) {
                    JobTitle::insert($insertArr);
                }
            });

            DB::commit();
            $this->info('Migration of old data for table (titles) completed.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error('Migration failed: '.$e->getMessage());

            return;
        }

        try {
            $insertArr = [];
            $this->info('Migrating old data for table (user_job_titles).');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_job_titles')->chunkById(1000, function ($userJobs) use (&$insertArr) {
                foreach ($userJobs as $userJob) {
                    if (!DB::table('users')->where('id', $userJob->user_id)->exists()) {
                        continue;
                    }

                    $userJobs_details = [
                        'id'                => $userJob->id,
                        'user_id'           => $userJob->user_id,
                        'job_title_id'      => $userJob->title_id,
                        'pinned'            => $userJob->pinned,
                        'created_at'        => $userJob->created_at,
                        'updated_at'        => $userJob->updated_at,
                    ];

                    $check_userJobs = UserJobTitle::find($userJob->id);
                    if (!$check_userJobs) {
                        $insertArr[] = $userJobs_details;
                    }
                }
                if (!empty($insertArr)) {
                    UserJobTitle::insert($insertArr);
                    $insertArr = [];
                }
            });
            DB::commit();
            $this->info('Migration of old data for table (user_job_titles) completed.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            $this->error($e->getMessage());
            DB::rollback();

            return;
        }

        try {
            $insertArr = [];
            $this->info('Migrating old data for table (related_titles).');
            DB::beginTransaction();
            DB::connection('mysql2')->table('related_titles')->chunkById(1000, function ($relatedJobs) use ($insertArr) {
                foreach ($relatedJobs as $relatedJob) {
                    $relatedJobs_details = [
                        'id'                      => $relatedJob->id,
                        'job_title_id'            => $relatedJob->title_id,
                        'related_job_title_id'    => $relatedJob->related_title_id,
                        'created_at'              => Carbon::now(),
                        'updated_at'              => Carbon::now(),
                    ];

                    $check_relatedJobs = RelatedJobTitle::find($relatedJob->id);
                    if (!$check_relatedJobs) {
                        $insertArr[] = $relatedJobs_details;
                    }
                }
                RelatedJobTitle::insert($insertArr);
            });
            DB::commit();
            $this->info('Migrating of old data for table (related_titles) completed.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            $this->error($e->getMessage());
            DB::rollback();

            return;
        }

        try {
            $insertArr = [];
            $this->info('Migrating old data for table (title_skills).');

            DB::connection('mysql2')->table('title_skills')->chunkById(1000, function ($jobSkills) use ($insertArr) {
                foreach ($jobSkills as $jobSkill) {
                    $jobSkills_details = [
                        'id'                      => $jobSkill->id,
                        'job_title_id'            => $jobSkill->title_id,
                        'skill_id'                => $jobSkill->skill_id,
                        'created_at'              => Carbon::now(),
                        'updated_at'              => Carbon::now(),
                    ];

                    $check_jobSkills = JobTitleSkill::find($jobSkill->id);
                    if (!$check_jobSkills) {
                        $insertArr[] = $jobSkills_details;
                    }
                }
                JobTitleSkill::insert($insertArr);
            });
            DB::commit();
            $this->info('Migrating of old data for table (title_skills) completed.');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            $this->error($e->getMessage());
            DB::rollback();

            return;
        }

        $this->info('Migrating of old data for tables (titles, user_job_titles, related_titles & title_skills) completed.');
    }
}
