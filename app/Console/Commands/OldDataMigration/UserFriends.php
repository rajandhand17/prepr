<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

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
    protected $description = 'This command will migrate all users friends';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users friends table started.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('friends')->chunkById(1000, function ($friends, $key) {
                foreach ($friends as $single_user_friends) {
                    // Check if both users exist
                    $checkUser = \App\Models\User::find($single_user_friends->user_id);
                    $checkUserReference = \App\Models\User::find($single_user_friends->ref_id);

                    if ($checkUser === null || $checkUserReference === null) {
                        continue;
                    }

                    // Retrieve an existing Friend or create a new one
                    $userFriends = \App\Models\Friend::firstOrNew(['id' => $single_user_friends->id]);

                    // Parse date fields with Carbon or set them to null if empty
                    $createdAt = !empty($single_user_friends->created_at) ? Carbon::createFromTimestamp($single_user_friends->created_at) : null;
                    $updatedAt = !empty($single_user_friends->updated_at) ? Carbon::createFromTimestamp($single_user_friends->updated_at) : null;
                    $deletedAt = !empty($single_user_friends->deleted_at) ? Carbon::createFromTimestamp($single_user_friends->deleted_at) : null;

                    // Fill the model attributes
                    $userFriends->fill([
                        'user_id'        => $single_user_friends->user_id,
                        'reference_id'   => $single_user_friends->ref_id,
                        'status'         => $single_user_friends->status,
                        'user_follow'    => $single_user_friends->follow,
                        'newsfeed'       => $single_user_friends->newsfeed,
                        'created_at'     => $createdAt,
                        'updated_at'     => $updatedAt,
                        'deleted_at'     => $deletedAt,
                    ]);

                    // Save the model
                    $userFriends->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users friends table completed.');
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
