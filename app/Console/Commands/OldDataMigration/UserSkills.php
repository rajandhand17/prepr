<?php

namespace App\Console\Commands\OldDataMigration;

use DB;
use Illuminate\Console\Command;

class UserSkills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-skills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will migrate all users skills data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users skills table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_skills')->chunkById(1000, function ($userSkills) {
                foreach ($userSkills as $skill) {
                    $checkUsers = \App\Models\User::find($skill->user_id);
                    if ($checkUsers == null) {
                        continue;
                    }
                    $userSkillsExistsOrNot = \App\Models\UserSkills::where('id', $skill->id)->first();
                    if ($userSkillsExistsOrNot) {
                        $userSkill = $userSkillsExistsOrNot;
                    } else {
                        $userSkill = new \App\Models\UserSkills();
                    }
                    $userSkill->user_id = $skill->user_id;
                    $userSkill->skill = $skill->skill;
                    $userSkill->pinned = $skill->pinned;
                    $userSkill->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users skills table completed.');
        } catch(\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
