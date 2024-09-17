<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Project;
use App\Models\ProjectSocialActivity;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProjectVoteData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-vote-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for migrate project vote data from legacy to learnlab';

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
            $this->info('Started Migration for project vote data from legacy to learnlab');
            DB::beginTransaction();

            // Fetch all projects, including soft-deleted ones
            $projects = Project::withTrashed()->get();

            if ($projects->isNotEmpty()) {
                foreach ($projects as $project) {
                    // For project votes
                    $projectVotes = DB::connection('mysql2')->table('project_votes')->where(['project_id' => $project->id])->get();
                    if ($projectVotes->isNotEmpty()) {
                        foreach ($projectVotes as $projectVote) {
                            $checkUser = User::find($projectVote->user_id);
                            if ($checkUser && $projectVote->vote == '1') {
                                $createdAt = $projectVote->created_at != null ? Carbon::createFromTimestamp($projectVote->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $updatedAt = $projectVote->updated_at != null ? Carbon::createFromTimestamp($projectVote->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                                $deletedAt = $projectVote->deleted_at != null ? Carbon::createFromTimestamp($projectVote->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                                $newProjectVote = new ProjectSocialActivity();
                                $newProjectVote->id = $projectVote->id;
                                $newProjectVote->user_id = $projectVote->user_id;
                                $newProjectVote->project_id = $project->id;
                                $newProjectVote->vote = '1';
                                $newProjectVote->created_at = $createdAt;
                                $newProjectVote->updated_at = $updatedAt;
                                $newProjectVote->deleted_at = $deletedAt;
                                $newProjectVote->save();
                            }
                        }
                    }
                }
            }

            DB::commit();
            $this->info('Completed Migration from legacy to learn-lab db for project vote data');
        } catch (Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error($e->getMessage());
        }
    }
}
