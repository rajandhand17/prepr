<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Skill;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class Skills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:skills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old skill table data to new db structure.';

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
            $this->info('Migrating old data for skills table.');
            DB::beginTransaction();

            DB::connection('mysql2')->table('skills')->chunkById(1000, function ($skills) use ($insertArr) {
                foreach ($skills as $skill) {
                    $skills_details = [
                        'id'          => $skill->id,
                        'title'       => $skill->skill,
                        'fr_CA_title' => $skill->fr_CA_skill,
                        'created_at'  => $skill->created_at,
                        'updated_at'  => $skill->updated_at,
                    ];
                    $check_skills = Skill::find($skill->id);
                    if (!$check_skills) {
                        $insertArr[] = $skills_details;
                    }
                }
                Skill::insert($insertArr);
            });
            DB::commit();
            $this->info('Migrating of old data for skills table completed.');

            return;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
