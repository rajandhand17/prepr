<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Project as Projects;

class ProjectIndustry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project table data to new db structure.';

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

            $this->info('Migrating old data for project table.');
            DB::beginTransaction();

            $project = DB::connection('mysql2')->table('project')->get();
            if($project->count() > 0){
                 
                foreach ($project as $key => $single_project){
                   $project_details=[
                        'language' => $single_project->language,
                        'slug' => $single_project->slug,
                        'user_id' => $single_project->user_id,
                        'reference_type' => $single_project->reference_type,
                        'reference_id' => $single_project->reference_id,
                        'lab_id' => $single_project->lab_id,
                        'privacy' => $single_project->privacy,
                        'file_download_privacy' => $single_project->file_download_privacy,
                        'team' => $single_project->team,
                        'enable_team_chat' => $single_project->enable_team_chat,
                        'is_alert_sent' => $single_project->is_alert_sent,
                        'stage' => $single_project->stage,
                        'status' => $single_project->status,
                        'recruiting_status' => $single_project->recruiting_status,
                        'type' => $single_project->type,
                        'industry' => $single_project->industry,
                        'verticals' => $single_project->verticals,
                        'media_type' => $single_project->media_type,
                        'image' => $single_project->image,
                        'title' => $single_project->title,
                        'description' => $single_project->description,
                        'date' => $single_project->date,
                        'category' => $single_project->category,
                        'total_share' => $single_project->total_share,
                        'user_social_links' => $single_project->user_social_links,
                        'associate_lab' => $single_project->associate_lab,
                        'univercity' => $single_project->univercity,
                        'coworking_space' => $single_project->coworking_space,
                        'tecnology' => $single_project->tecnology,
                        'incubator' => $single_project->incubator,
                        'skills' => $single_project->skills,
                        'challenge_id' => $single_project->challenge_id,
                        'start_challenge' => $single_project->start_challenge,
                    ];
                    $check_project = Projects::where($project_details)->first();
                    if(!$check_project){
                        Projects::create($project_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for project table completed.');
                return;
            }
            DB::rollback();
            $this->error('No project found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
