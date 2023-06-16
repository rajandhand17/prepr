<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Skill;
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
            $this->info('Migrating old data for skills table.');
            DB::beginTransaction();

            $skills = DB::connection('mysql2')->table('skills')->get();
            if ($skills->count() > 0) {
                foreach ($skills as $key => $single_skill) {
                    $skills_details = [
                        'name'       => $single_skill->skill,
                        'fr_CA_name' => $single_skill->fr_CA_skill,
                    ];
                    $check_skills = Skill::where($skills_details)->first();
                    if (!$check_skills) {
                        Skill::create($skills_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for skills table completed.');

                return;
            }
            DB::rollback();
            $this->error('No skill found.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
