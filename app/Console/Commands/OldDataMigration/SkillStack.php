<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
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
                    $check_skill_stacks = SkillStacks::where('title', $single_skill_stack->title)->first();
                    if ($check_skill_stacks) {
                        $newSkillStack = $check_skill_stacks;
                    } else {
                        $newSkillStack = new SkillStacks();
                    }
                    $newSkillStack->id = $single_skill_stack->id;
                    $newSkillStack->title = $single_skill_stack->title;
                    $newSkillStack->fr_CA_title = $single_skill_stack->fr_CA_title;
                    $newSkillStack->description = $single_skill_stack->description;
                    $newSkillStack->fr_CA_description = $single_skill_stack->fr_CA_description;
                    $newSkillStack->created_at  = $single_skill_stack->created_at;
                    $newSkillStack->updated_at  = $single_skill_stack->updated_at;
                    $newSkillStack->skills = $skills;
                    $newSkillStack->save();
                }
                DB::commit();
                $this->info('Migrating of old data for skill stack table completed.');

                return;
            }
            DB::rollback();
            $this->error('No skill stack found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
