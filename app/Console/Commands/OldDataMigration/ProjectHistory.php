<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Project;
use App\Models\ProjectHistory as ProjectHistoryModel;
use App\Models\User;
use DB;
use Illuminate\Console\Command;
use Exception;

class ProjectHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for migrate project history from legacy to learnlab';

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
            $this->info('Started Migration for project history from legacy to learnlab');
            DB::beginTransaction();

            $projectActivities = DB::connection('mysql2')->table('activity_log')
                                        ->select('activity_log.*', 'projects.title as title', 'projects.id as project_id', 'users.name')
                                        ->where('subject_type', 'App\\Models\\Project')
                                        ->orderBy('created_at', 'desc')
                                        ->leftJoin('projects', 'activity_log.subject_id', '=', 'projects.id')
                                        ->leftJoin('users', function($join) {
                                            $join->on('activity_log.causer_id', '=', 'users.id')
                                                ->whereNull('users.deleted_at'); 
                                        })->get();
            if($projectActivities){
                foreach ($projectActivities as $activity) {
                    if(!empty($activity->subject_id) && !empty($activity->causer_id)){
                        if(Project::where('id',$activity->subject_id)->exists() && User::where('id',$activity->causer_id)->exists()){
                            if($activity->subject_id == $activity->project_id){
                                $this->getHistoryData($activity);
                            }
                        }
                    }
                }
            }
            
            DB::commit();
            $this->info('Completed Migration from legacy to learn-lab db for project history');
        } catch (Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
        }
    }

    public function getHistoryData($activity){
        try {
            $userName = $activity->name ? $activity->name : 'user';
            switch ($activity->description) {
                case 'created':
                    $activityDescription = $userName.' has created the Project:- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;

                case 'submitted_late':
                    $activityDescription = $userName.' has late submitted the Project:- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;

                case 'submitted':
                    $activityDescription = $userName.' has submitted the Project:- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;

                case 'deleted':
                    $this->deletedActions($activity,$userName);
                    break;

                case 'updated':
                    $this->updatedActions($activity,$userName);
                    break;
            }
        } catch (Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
        }
    }

    public function updatedActions($activity,$userName){
        try {
            switch ($activity->log_name) {
                case 'project':
                    $activityDescription = $userName.' has updated the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'project_pitch':
                    $activityDescription = $userName.' has updated the project pitch from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'pitch_template':
                    $activityDescription = $userName.' has updated the project pitch template from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'project_file':
                    $activityDescription = $userName.' has updated the project files from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'project_gallery':
                    $activityDescription = $userName.' has updated the project gallery from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'project_gallery_video':
                    $activityDescription = $userName.' has updated the project gallery and video from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'projectTask':
                    $activityDescription = $userName.' has updated the project task from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
                case 'recruiting_status':
                    $activityDescription = $userName.' has updated the recruiting status from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'team_leader':
                    $activityDescription = $userName.' has updated the team leader from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'associated_lab':
                    $activityDescription = $userName.' has updated the labs from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'skills':
                    $activityDescription = $userName.' has updated the skills from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'challenge':
                    $activityDescription = $userName.' has updated the challenge from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'trophy':
                    $activityDescription = $userName.' has updated a trophy from the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
            }
        } catch (Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
        }
    }

    public function deletedActions($activity,$userName){
        try {
            switch ($activity->log_name) {
                case 'project_file':
                        $activityDescription = $userName.' has removed a file from files in the Project :- '.$activity->title;
                        $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'project_gallery':
                        $activityDescription = $userName.' has removed a image from gallery in the Project :- '.$activity->title;
                        $this->saveHistory($activity,$activityDescription);
                    break;
    
                case 'project_gallery_video':
                    $activityDescription = $userName.' has removed a video from gallery in the Project :- '.$activity->title;
                    $this->saveHistory($activity,$activityDescription);
                    break;
            }
        } catch (Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
        }
    }

    public function saveHistory($activity,$activityDescription){
        try {
            ProjectHistoryModel::create(['project_id' => $activity->subject_id, 'user_id' => $activity->causer_id, 'activity' => $activityDescription,'created_at' => $activity->created_at,'updated_at' => $activity->updated_at]);
        } catch (Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
        }
                                  
    }
}