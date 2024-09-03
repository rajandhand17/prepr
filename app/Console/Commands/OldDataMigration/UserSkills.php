<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
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
    protected $description = 'This command will migrate all users skills';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users skills table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_skills')->chunkById(1000, function ($userSkills, $key) {
                foreach ($userSkills as $single_user_skill) {
                    // Check if the user and skill exist
                    $checkUser = \App\Models\User::find($single_user_skill->user_id);
                    $checkSkill = \App\Models\Skill::find($single_user_skill->skill);
                    if ($checkUser === null || $checkSkill === null) {
                        continue;
                    }
                    // Retrieve an existing UserSkills or create a new one
                    $userSkills = \App\Models\UserSkills::findOrNew($single_user_skill->id);

                    // Parse date fields with Carbon or set them to null if empty
                    $createdAt = !empty($single_user_skill->created_at) ? Carbon::parse($single_user_skill->created_at) : null;
                    $updatedAt = !empty($single_user_skill->updated_at) ? Carbon::parse($single_user_skill->updated_at) : null;
                    $deletedAt = !empty($single_user_skill->deleted_at) ? Carbon::parse($single_user_skill->deleted_at) : null;

                    // Fill the model attributes
                    $userSkills->fill([
                        'user_id'      => $single_user_skill->user_id,
                        'skill'        => $single_user_skill->skill,
                        'pinned'       => $single_user_skill->pinned,
                        'created_at'   => $createdAt,
                        'updated_at'   => $updatedAt,
                        'deleted_at'   => $deletedAt,
                    ]);

                    // Save the model
                    $userSkills->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users skills table completed.');
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
