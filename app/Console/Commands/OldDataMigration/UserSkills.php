<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // Corrected model name to match Laravel naming conventions

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
    protected $description = 'Migrate old data for users skills.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating old data for users skills table.');

        try {
            DB::beginTransaction();

            DB::connection('mysql2')->table('user_skills')->chunkById(1000, function ($userSkills) {
                $userIds = $userSkills->pluck('user_id')->unique()->toArray();
                $skillIds = $userSkills->pluck('skill')->unique()->toArray();

                // Fetch existing users and skills
                $existingUsers = User::whereIn('id', $userIds)->pluck('id')->toArray();
                $existingSkills = Skill::whereIn('id', $skillIds)->pluck('id')->toArray();

                foreach ($userSkills as $singleUserSkill) {
                    if (!in_array($singleUserSkill->user_id, $existingUsers) || !in_array($singleUserSkill->skill, $existingSkills)) {
                        continue;
                    }

                    UserSkill::updateOrCreate(
                        ['id' => $singleUserSkill->id],
                        [
                            'user_id'      => $singleUserSkill->user_id,
                            'skill'        => $singleUserSkill->skill,
                            'pinned'       => $singleUserSkill->pinned,
                            'created_at'   => $this->parseDate($singleUserSkill->created_at),
                            'updated_at'   => $this->parseDate($singleUserSkill->updated_at),
                            'deleted_at'   => $this->parseDate($singleUserSkill->deleted_at),
                        ]
                    );
                }
            });

            DB::commit();
            $this->info('Migration of old data for users skills table completed.');
        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error: '.$e->getMessage());
        }
    }

    /**
     * Parse a timestamp or return null if empty.
     *
     * @param mixed $timestamp
     *
     * @return \Carbon\Carbon|null
     */
    private function parseDate($timestamp)
    {
        return !empty($timestamp) ? Carbon::parse($timestamp) : null;
    }
}
