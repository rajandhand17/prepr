<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Project;
use App\Models\ProjectExternalLink as ModelsProjectExternalLink;
use App\Models\SocialLink;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProjectExternalLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-external-links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for migrate project external links from legacy to learnlab';

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
            $this->info('Started Migration for project external links from legacy to learnlab');
            DB::beginTransaction();

            // Fetch data from old table
            $projectExternalLinks = DB::connection('mysql2')->table('user_sociallink')->get();

            if ($projectExternalLinks->isNotEmpty()) {
                foreach ($projectExternalLinks as $externalLink) {
                    // Check if project exists
                    $existingProject = Project::withTrashed()->where('id', $externalLink->project_id)->first();
                    if ($existingProject == null) {
                        continue;
                    }
                    // Delete existing external links for the project
                    ModelsProjectExternalLink::where('project_id', $externalLink->project_id)->delete();
                    if (!empty($externalLink->link_url)) {
                        // Retrieve timestamps, safely handling null values
                        $createdAt = $externalLink->created_at ? Carbon::createFromTimestamp($externalLink->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                        $updatedAt = $externalLink->updated_at ? Carbon::createFromTimestamp($externalLink->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                        $deletedAt = $externalLink->deleted_at ? Carbon::createFromTimestamp($externalLink->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                        // Find the associated social link, including soft-deleted ones
                        $checkSocialLink = SocialLink::withTrashed()->find($externalLink->social_link_id);

                        // Create and save the new project external link
                        ModelsProjectExternalLink::create([
                            'id' => $externalLink->id,
                            'project_id' => $externalLink->project_id,
                            'social_media_link' => $externalLink->link_url,
                            'social_link_id' => $checkSocialLink ? $externalLink->social_link_id : '15',
                            'created_at' => $createdAt,
                            'updated_at' => $updatedAt,
                            'deleted_at' => $deletedAt
                        ]);
                    }
                }
            }

            DB::commit();
            $this->info('Completed Migration from legacy to learn-lab db for project external links');
        } catch (Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);

            $this->error($e->getMessage());
        }
    }
}
