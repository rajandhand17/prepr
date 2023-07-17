<?php

namespace App\Console\Commands\OldDataMigration;

use DB;
use Illuminate\Console\Command;

class SkillGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:skill-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old skill groups table data to new db structure.';

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
            $this->info('Migrating old data for skill groups table.');
            DB::beginTransaction();

            $skill_groups = DB::connection('mysql2')->table('skill_groups')->get();
            if ($skill_groups->count() > 0) {
                foreach ($skill_groups as $key => $skill_group) {
                    $skills = [];
                    if ($skill_group->skills != null) {
                        if (str_contains($skill_group->skills, ',')) {
                            $skills = explode(',', $skill_group->skills);
                        } else {
                            $skills = [$skill_group->skills];
                        }
                    }
                    $skill_stacks = [];
                    if ($skill_group->skills != null) {
                        if (str_contains($skill_group->skill_stacks, ',')) {
                            $skill_stacks = explode(',', $skill_group->skills);
                        } else {
                            $skill_stacks = [$skill_group->skill_stacks];
                        }
                    }

                    $skill_group_details = [
                        'id'             => $skill_group->id,
                        'title'             => $skill_group->title,
                        'fr_CA_title'       => $skill_group->fr_CA_title,
                        'description'       => $skill_group->description,
                        'fr_CA_description' => $skill_group->fr_CA_description,
                        'skills'            => $skills,
                        'skill_stacks'      => $skill_stacks,
                    ];
                    $check_skill_group = \App\Models\SkillGroup::where('title', $skill_group->title)->first();
                    if (!$check_skill_group) {
                        \App\Models\SkillGroup::create($skill_group_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for skill groups table completed.');

                return;
            }
            DB::rollback();
            $this->error('No skill groups found.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
