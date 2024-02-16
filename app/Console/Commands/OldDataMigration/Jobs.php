<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Job;
use App\Models\JobSkill;
use App\Models\RelatedJob;
use App\Models\UserJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class Jobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:jobs';

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
            $insertArr = [];
            $this->info('Migrating old data for table (titles).');
            DB::beginTransaction();
            DB::connection('mysql2')->table('titles')->chunkById(1000, function ($jobs) use ($insertArr) {
                foreach ($jobs as $job) {
                    $jobs_details = [
                        'id'          => $job->id,
                        'title'       => $job->name,
                        'fr_CA_title' => $job->fr_CA_name,
                        'lc_id'       => $job->lc_id,
                        'created_at'  => Carbon::now(),
                        'updated_at'  => Carbon::now(),
                    ];
                    $check_jobs = Job::find($job->id);
                    if (!$check_jobs) {
                        $insertArr[] = $jobs_details;
                    }
                }
                Job::insert($insertArr);
            });
            DB::commit();
            $this->info('Migrating of old data for table (titles) completed.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            DB::rollback();
            return;
        }

        try {
            $insertArr = [];
            $this->info('Migrating old data for table (user_job_titles).');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_job_titles')->chunkById(1000, function ($userJobs) use ($insertArr) {
                foreach ($userJobs as $userJob) {
                    $userJobs_details = [
                        'id'          => $userJob->id,
                        'user_id'     => $userJob->user_id,
                        'job_id'      => $userJob->title_id,
                        'pinned'      => $userJob->pinned,
                        'created_at'  => Carbon::now(),
                        'updated_at'  => Carbon::now(),
                    ];

                    $check_userJobs = UserJob::find($userJob->id);
                    if (!$check_userJobs) {
                        $insertArr[] = $userJobs_details;
                    }
                }
                $this->info(json_encode($insertArr));
                UserJob::insert($insertArr);
            });
            DB::commit();
            $this->info('Migrating of old data for table (user_job_titles) completed.');
        } catch (\Exception $e) {
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
                        'id'                => $relatedJob->id,
                        'job_id'            => $relatedJob->title_id,
                        'related_job_id'    => $relatedJob->related_title_id,
                        'created_at'        => Carbon::now(),
                        'updated_at'        => Carbon::now(),
                    ];

                    $check_relatedJobs = RelatedJob::find($relatedJob->id);
                    if (!$check_relatedJobs) {
                        $insertArr[] = $relatedJobs_details;
                    }
                }
                RelatedJob::insert($insertArr);
            });
            DB::commit();
            $this->info('Migrating of old data for table (related_titles) completed.');
        } catch (\Exception $e) {
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
                        'id'                => $jobSkill->id,
                        'job_id'            => $jobSkill->title_id,
                        'skill_id'          => $jobSkill->skill_id,
                        'created_at'        => Carbon::now(),
                        'updated_at'        => Carbon::now(),
                    ];

                    $check_jobSkills = JobSkill::find($jobSkill->id);
                    if (!$check_jobSkills) {
                        $insertArr[] = $jobSkills_details;
                    }
                }
                JobSkill::insert($insertArr);
            });
            DB::commit();
            $this->info('Migrating of old data for table (title_skills) completed.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            DB::rollback();
            return;
        }

        $this->info('Migrating of old data for tables (titles, user_job_titles, related_titles & title_skills) completed.');
        return;
    }
}
