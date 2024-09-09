<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Project;
use App\Models\ProjectFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProjectFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-files-galleries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for migrate project files & galleries from legacy to learnlab';

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
            $this->info('Started Migration for project files and galleries from legacy to learnlab');
            DB::beginTransaction();

            // Fetch data from both tables and merge them
            $projectFiles = DB::connection('mysql2')->table('project_files')->get();
            $projectGalleries = DB::connection('mysql2')->table('project_galleries')->get();

            // Merge both collections
            $allFiles = $projectFiles->merge($projectGalleries);
            if ($allFiles->isNotEmpty()) {
                foreach ($allFiles as $fileData) {
                    // Check if project exists
                    $existingProject = Project::withTrashed()->where('id', $fileData->project_id)->first();
                    if ($existingProject == null) {
                        continue;
                    }
                    $extensionFetch = strtolower(pathinfo($fileData->original, PATHINFO_EXTENSION));
                    $fileType = $this->getFileType($extensionFetch);

                    if ($fileType !== null) {
                        // Use Carbon::createFromTimestamp only when dates are not null
                        $createdAt = $fileData->created_at ? Carbon::createFromTimestamp($fileData->created_at)->translatedFormat('Y-m-d H:i:s') : null;
                        $updatedAt = $fileData->updated_at ? Carbon::createFromTimestamp($fileData->updated_at)->translatedFormat('Y-m-d H:i:s') : null;
                        $deletedAt = $fileData->deleted_at ? Carbon::createFromTimestamp($fileData->deleted_at)->translatedFormat('Y-m-d H:i:s') : null;

                        // Fetch the file type from constants
                        $file_type = match ($fileType) {
                            'image'    => config('constants.project_file_type.image'),
                            'audio'    => config('constants.project_file_type.audio'),
                            'document' => config('constants.project_file_type.docs'),
                            'video'    => config('constants.project_file_type.video'),
                            default    => null
                        };

                        if ($file_type !== null) {
                            $newProjectFile = new ProjectFile();
                            $newProjectFile->project_id = $existingProject->id;
                            $newProjectFile->title = $fileData->original;
                            $newProjectFile->path = $fileData->name;
                            $newProjectFile->type = $file_type;
                            $newProjectFile->created_at = $createdAt;
                            $newProjectFile->updated_at = $updatedAt;
                            $newProjectFile->deleted_at = $deletedAt;
                            $newProjectFile->save();
                        }
                    }
                }
            }

            DB::commit();
            $this->info('Completed Migration from legacy to learn-lab db for project files and galleries');
        } catch (Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);

            $this->error($e->getMessage());
        }
    }

    private function getFileType($extension)
    {
        try {
            $imageExtensions = ['jpg', 'jpeg', 'webp', 'png'];
            $audioExtensions = ['mp3'];
            $docExtensions = ['pdf', 'doc', 'docx', 'xlsx', 'xls', 'pptx', 'pptm', 'odp', 'ppt'];
            $videoExtensions = ['mp4', 'mov', 'wmv', 'avi', 'webm', 'mkv', 'mpeg-2'];

            if (in_array($extension, $imageExtensions)) {
                return 'image';
            } elseif (in_array($extension, $audioExtensions)) {
                return 'audio';
            } elseif (in_array($extension, $docExtensions)) {
                return 'document';
            } elseif (in_array($extension, $videoExtensions)) {
                return 'video';
            }

            return null; // Return null if no valid type is found
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            $this->error($e->getMessage());

            return null;
        }
    }
}
