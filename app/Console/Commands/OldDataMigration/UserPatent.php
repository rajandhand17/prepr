<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class UserPatent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-patent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will migrate all users patents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users patents table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_patents')->chunkById(1000, function ($userPatents, $key) {
                foreach ($userPatents as $single_user_patents) {
                    // Check if the user exists
                    $checkUser = \App\Models\User::find($single_user_patents->user_id);
                    if ($checkUser === null) {
                        continue;
                    }
                    // Find an existing UserPatent or create a new one
                    $userPatent = \App\Models\UserPatent::findOrNew($single_user_patents->id);
                    $userPatent->fill([
                        'user_id'      => $single_user_patents->user_id,
                        'title'        => $single_user_patents->title,
                        'name'         => $single_user_patents->name,
                        'patent_date'  => $single_user_patents->patent_date,
                        'description'  => $single_user_patents->description,
                        'created_at'   => !empty($single_user_patents->created_at) ? Carbon::createFromTimestamp($single_user_patents->created_at) : null,
                        'updated_at'   => !empty($single_user_patents->updated_at) ? Carbon::createFromTimestamp($single_user_patents->updated_at) : null,
                        'deleted_at'   => !empty($single_user_patents->deleted_at) ? Carbon::createFromTimestamp($single_user_patents->deleted_at) : null,
                    ]);
                    $userPatent->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users patents table completed.');
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
