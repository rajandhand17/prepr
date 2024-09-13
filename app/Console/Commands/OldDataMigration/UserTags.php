<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserTag;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old data for users tags.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating old data for users tags table.');

        try {
            DB::beginTransaction();

            DB::connection('mysql2')->table('user_tags')->chunkById(1000, function ($userTags) {
                $userIds = $userTags->pluck('user_id')->unique()->toArray();
                $tagIds = $userTags->pluck('tags_id')->unique()->toArray();

                $existingUsers = User::whereIn('id', $userIds)->pluck('id')->toArray();
                $existingTags = Tag::whereIn('id', $tagIds)->pluck('id')->toArray();

                foreach ($userTags as $singleUserTag) {
                    if (!in_array($singleUserTag->user_id, $existingUsers) || !in_array($singleUserTag->tags_id, $existingTags)) {
                        continue;
                    }

                    UserTag::updateOrCreate(
                        ['id' => $singleUserTag->id],
                        [
                            'user_id'    => $singleUserTag->user_id,
                            'tag_id'     => $singleUserTag->tags_id,
                            'created_at' => $this->parseDate($singleUserTag->created_at),
                            'updated_at' => $this->parseDate($singleUserTag->updated_at),
                            'deleted_at' => $this->parseDate($singleUserTag->deleted_at),
                        ]
                    );
                }
            });

            DB::commit();
            $this->info('Migration of old data for users tags table completed.');
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
        return !empty($timestamp) ? Carbon::createFromTimestamp($timestamp) : null;
    }
}
