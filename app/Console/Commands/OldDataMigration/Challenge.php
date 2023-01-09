<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Challange as Challanges;

class Challenge extends Command
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:Challenge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old challange table data to new db structure.';

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

            $this->info('Migrating old data for challange table.');
            DB::beginTransaction();

            $challanges = DB::connection('mysql2')->table('challanges')->get();

            if($challanges->count() > 0){
                foreach ($challanges as $key => $single_challanges){
                    $challanges_details=[
                        'language' => $single_challanges->language,
                        'slug' => $single_challanges->slug,
                        'user_id' => $single_challanges->user_id,
                        'organisation' => $single_challanges->organisation,
                        'host_id' => $single_challanges->host_id,
                        'title' => $single_challanges->title,
                        'verification' => $single_challanges->verification,
                        'description' => $single_challanges->description,
                        'sourcelink' => $single_challanges->sourcelink,
                        'category' => $single_challanges->category,
                        'challange_skill' => $single_challanges->challange_skill,
                        'challange_tag' => $single_challanges->challange_tag,
                        'tags' => $single_challanges->tags,
                        'associat_lab' => $single_challanges->associat_lab,
                        'status' => $single_challanges->status,
                        'project_privacy' => $single_challanges->project_privacy,
                        'deadline' => $single_challanges->deadline,
                        'dates' => $single_challanges->dates,
                        'flexibleDateNumber' => $single_challanges->flexibleDateNumber,
                        'flexibleExpireDateDuration' => $single_challanges->flexibleExpireDateDuration,
                        'flexibleExpireDate' => $single_challanges->flexibleExpireDate,
                        'automaticAlert' => $single_challanges->automaticAlert,
                        'submission_deadline_date_desc' => $single_challanges->submission_deadline_date_desc,
                        'min_ranks' => $single_challanges->min_ranks,
                        'min_points' => $single_challanges->min_points,
                        'projectSubmissionRequirements' => $single_challanges->projectSubmissionRequirements,
                        'submitProject' => $single_challanges->submitProject,
                        'maxProjectSubmission' => $single_challanges->maxProjectSubmission,
                        'maxAssociatedProjects' => $single_challanges->maxAssociatedProjects,
                        'completeEducationProgram' => $single_challanges->completeEducationProgram,
                        'requirementProgram' => $single_challanges->requirementProgram,
                        'minExperience' => $single_challanges->minExperience,
                        'minImportedBadges' => $single_challanges->minImportedBadges,
                        'mminAchievementTrophies' => $single_challanges->minAchievementTrophies,
                        'additional_info' => $single_challanges->additional_info,
                        'application_deadline' => $single_challanges->application_deadline,
                        'length' => $single_challanges->length,
                        'last_registration_date' => $single_challanges->last_registration_date,
                        'call_date' => $single_challanges->call_date,
                        'mediaType' => $single_challanges->mediaType,
                        'cover_image' => $single_challanges->cover_image,
                        'privacy' => $single_challanges->privacy,
                        'total_share' => $single_challanges->total_share,
                        'user_count' => $single_challanges->user_count,
                        'is_completed' => $single_challanges->is_completed,
                        'agreement' => $single_challanges->agreement,
                        'type' => $single_challanges->type,
                        'price'=> $single_challanges->price,
                        'code'=> $single_challanges->code,
                        'subject_line'=> $single_challanges->subject_line,
                        'email_message'=> $single_challanges->email_message,
                        'application_dateline_desc'=> $single_challanges->application_dateline_desc,
                        'call_date_desc'=> $single_challanges->call_date_desc,
                        'last_registration_date_desc'=> $single_challanges->last_registration_date_desc,
                        'assessment_date'=> $single_challanges->assessment_date,
                        'assessment_date_desc'=> $single_challanges->assessment_date_desc,
                        'notify_participants'=> $single_challanges->notify_participants,
                        'published'=> $single_challanges->published,
                    ];
                    $check_category = Challanges::where($challanges_details)->first();
                    if(!$check_category){
                        Challanges::create($challanges_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for challanges table completed.');
                return;
            }
            DB::rollback();
            $this->error('No challanges found.');

        } catch (\Exception $e) {
        
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
