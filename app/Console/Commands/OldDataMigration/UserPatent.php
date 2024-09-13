<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Models\UserPatent as ModelUserPatent;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Migrate old data for users patents.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating old data for users patents table.');

        try {
            DB::beginTransaction();

            DB::connection('mysql2')->table('user_patents')->chunkById(1000, function ($userPatents) {
                $userIds = $userPatents->pluck('user_id')->unique()->toArray();
                $existingUsers = User::whereIn('id', $userIds)->pluck('id')->toArray();

                foreach ($userPatents as $singleUserPatent) {
                    if (!in_array($singleUserPatent->user_id, $existingUsers)) {
                        continue;
                    }

                    ModelUserPatent::updateOrCreate(
                        ['id' => $singleUserPatent->id],
                        [
                            'user_id'      => $singleUserPatent->user_id,
                            'title'        => $singleUserPatent->title,
                            'name'         => $singleUserPatent->name,
                            'patent_date'  => $this->parseDate($singleUserPatent->patent_date),
                            'description'  => $singleUserPatent->description,
                            'created_at'   => $this->parseDate($singleUserPatent->created_at),
                            'updated_at'   => $this->parseDate($singleUserPatent->updated_at),
                            'deleted_at'   => $this->parseDate($singleUserPatent->deleted_at),
                        ]
                    );
                }
            });

            DB::commit();
            $this->info('Migration of old data for users patents table completed.');
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
