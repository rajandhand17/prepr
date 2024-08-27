<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\Lab;
use App\Models\Project as ModelsProject;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Project extends Command
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
    protected $description = 'This command is use to migrate old Projects table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating of old data for Projects table started.');
            // DB::beginTransaction();

            // Fetch Projects from Legacy Database in chucks of 1000 data
            DB::connection('mysql2')->table('projects')->chunkById(1000, function ($projects) {
                foreach ($projects as $key => $project) {
                    // Fetch User based on user_id
                    $checkUser = User::find($project->user_id);
                    if (!$checkUser) {
                        continue;
                    }

                    $fetchChallenge = Challenge::find($project->challenge_id);
                    $fetchLab = Lab::find($project->lab_id);

                    switch ($project->privacy) {
                        case 'public':
                            $projectPrivacy = '0';
                            break;
                        case 'private':
                            $projectPrivacy = '1';
                            break;
                        default:
                            $projectPrivacy = '0';
                            break;
                    }

                    switch ($project->file_download_privacy) {
                        case 'public':
                            $projectDownloadPrivacy = '0';
                            break;
                        case 'private':
                            $projectDownloadPrivacy = '1';
                            break;
                        default:
                            $projectDownloadPrivacy = '0';
                            break;
                    }

                    switch ($project->recruiting_status) {
                        case '0':
                            $projectRecruitingStatus = '0';
                            break;
                        case '1':
                            $projectRecruitingStatus = '1';
                            break;
                        default:
                            $projectRecruitingStatus = '0';
                            break;
                    }

                    $mediaType = 'image';
                    switch ($project->mediaType) {
                        case 'image':
                            $mediaType = '0';
                            break;
                        case 'embedded':
                            $mediaType = '1';
                            break;
                        case 'video':
                            $mediaType = '2';
                            break;
                        default:
                            $mediaType = '0';
                            break;
                    }

                    // For main Project table
                    $checkProject = ModelsProject::find($project->id);
                    if ($checkProject) {
                        $newProject = $checkProject;
                    } else {
                        $newProject = new ModelsProject();
                    }

                    dd($project, $projectPrivacy);
                }
            });
            // DB::commit();
            $this->info('Migrating of old data for Challanges table completed.');

            // return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            $this->error($e->getMessage());

            return;
        }
    }
}
