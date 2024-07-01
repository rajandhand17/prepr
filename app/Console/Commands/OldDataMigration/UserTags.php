<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

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
    protected $description = 'This command will migrate all users tags';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users tags table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_tags')->chunkById(1000, function ($userTags, $key) {
                foreach ($userTags as $single_user_tag) {
                    // Check if the user and tag exist
                    $checkUser = \App\Models\User::find($single_user_tag->user_id);
                    $checkTag = \App\Models\Tag::find($single_user_tag->tags_id);

                    if ($checkUser === null || $checkTag === null) {
                        continue;
                    }

                    // Retrieve an existing UserTag or create a new one
                    $userTag = \App\Models\UserTag::findOrNew($single_user_tag->id);

                    // Parse date fields with Carbon or set them to null if empty
                    $createdAt = !empty($single_user_tag->created_at) ? Carbon::createFromTimestamp($single_user_tag->created_at) : null;
                    $updatedAt = !empty($single_user_tag->updated_at) ? Carbon::createFromTimestamp($single_user_tag->updated_at) : null;
                    $deletedAt = !empty($single_user_tag->deleted_at) ? Carbon::createFromTimestamp($single_user_tag->deleted_at) : null;

                    // Fill the model attributes
                    $userTag->fill([
                        'user_id'      => $single_user_tag->user_id,
                        'tag_id'       => $single_user_tag->tags_id,
                        'created_at'   => $createdAt,
                        'updated_at'   => $updatedAt,
                        'deleted_at'   => $deletedAt,
                    ]);

                    // Save the model
                    $userTag->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users tags table completed.');
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
