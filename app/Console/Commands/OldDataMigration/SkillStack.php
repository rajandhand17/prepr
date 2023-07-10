<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\SkillStack as SkillStacks;
use DB;
use Illuminate\Console\Command;

class SkillStack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:skill-stacks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old skill stack table data to new db structure.';

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
            $this->info('Migrating old data for skill stack table.');
            DB::beginTransaction();

            $skill_stack = DB::connection('mysql2')->table('skill_stacks')->get();
            if ($skill_stack->count() > 0) {
                foreach ($skill_stack as $key => $single_skill_stack) {
                    $skills = [];
                    if ($single_skill_stack->skills != null) {
                        if (str_contains($single_skill_stack->skills, ',')) {
                            $skills = explode(',', $single_skill_stack->skills);
                        } else {
                            $skills = [$single_skill_stack->skills];
                        }
                    }
                    $skill_stack_details = [
                        'title'             => $single_skill_stack->title,
                        'fr_CA_title'       => $single_skill_stack->fr_CA_title,
                        'description'       => $single_skill_stack->description,
                        'fr_CA_description' => $single_skill_stack->fr_CA_description,
                        'skills'            => $skills,
                    ];
                    $check_skills = SkillStacks::where('title', $single_skill_stack->title)->first();
                    if (!$check_skills) {
                        SkillStacks::create($skill_stack_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for skill stack table completed.');

                return;
            }
            DB::rollback();
            $this->error('No skill stack found.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
