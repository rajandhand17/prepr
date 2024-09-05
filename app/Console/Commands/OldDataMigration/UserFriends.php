<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Friend;

class UserFriends extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-friends';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old data for users friends.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating old data for users friends table started.');

        try {
            DB::beginTransaction();

            DB::connection('mysql2')->table('friends')->chunkById(1000, function ($friends) {
                $userIds = $friends->pluck('user_id')->unique()->toArray();
                $refIds = $friends->pluck('ref_id')->unique()->toArray();

                $existingUsers = User::whereIn('id', array_merge($userIds, $refIds))->pluck('id')->toArray();

                foreach ($friends as $singleUserFriend) {
                    if (!in_array($singleUserFriend->user_id, $existingUsers) || !in_array($singleUserFriend->ref_id, $existingUsers)) {
                        continue;
                    }

                    // Use updateOrCreate for more efficient operations
                    Friend::updateOrCreate(
                        ['id' => $singleUserFriend->id],
                        [
                            'user_id'        => $singleUserFriend->user_id,
                            'reference_id'   => $singleUserFriend->ref_id,
                            'status'         => $singleUserFriend->status,
                            'user_follow'    => $singleUserFriend->follow,
                            'newsfeed'       => $singleUserFriend->newsfeed,
                            'created_at'     => $this->parseDate($singleUserFriend->created_at),
                            'updated_at'     => $this->parseDate($singleUserFriend->updated_at),
                            'deleted_at'     => $this->parseDate($singleUserFriend->deleted_at),
                        ]
                    );
                }
            });

            DB::commit();
            $this->info('Migrating old data for users friends table completed.');
        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Parse a timestamp or return null if empty.
     *
     * @param  mixed $timestamp
     * @return \Carbon\Carbon|null
     */
    private function parseDate($timestamp)
    {
        return !empty($timestamp) ? Carbon::createFromTimestamp($timestamp) : null;
    }
}
