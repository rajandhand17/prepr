<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ProjectVertical;
use DB;
use Illuminate\Console\Command;

class ProjectVerticals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:project-verticals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old project verticals table data to new db structure.';

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
            $this->info('Migrating old data for project verticals table.');
            DB::beginTransaction();

            $project_verticals = DB::connection('mysql2')->table('project_verticals')->get();
            if ($project_verticals->count() > 0) {
                foreach ($project_verticals as $key => $single_verticals) {
                    $check_project_verticals = ProjectVertical::where('title', $single_verticals->name)->first();
                    if ($check_project_verticals) {
                        $newProjectVertical = $check_project_verticals;
                    } else {
                        $newProjectVertical = new ProjectVertical();
                    }
                    $newProjectVertical->id = $single_verticals->id;
                    $newProjectVertical->title = $single_verticals->name;
                    $newProjectVertical->fr_CA_title = $single_verticals->fr_CA_name;
                    $newProjectVertical->save();
                }
                DB::commit();
                $this->info('Migrating of old data for project verticals table completed.');

                return;
            }
            DB::rollback();
            $this->error('No project verticals found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
