<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Models\UserAddress as ModelUserAddress;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class UserAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will migrate all users addresses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Starting migration of user addresses.');

            DB::beginTransaction();

            DB::connection('mysql2')->table('users')->chunkById(1000, function ($users) {
                foreach ($users as $user) {
                    // Check if the user exists in the main DB and skip if not
                    if (!User::where('id', $user->id)->exists()) {
                        continue;
                    }

                    // Retrieve an existing UserAddress or create a new one
                    $userAddress = ModelUserAddress::firstOrNew(['user_id' => $user->id]);

                    // Check if UserAddress already exists, skip if it does
                    if ($userAddress->exists) {
                        continue;
                    }

                    // Parse dates using Carbon or set to null
                    $userAddress->fill([
                        'user_id'    => $user->id,
                        'latitude'   => $user->latitude,
                        'longitude'  => $user->longitude,
                        'city'       => $user->city,
                        'address'    => $user->address,
                        'state'      => $user->states,
                        'country'    => $user->country,
                        'created_at' => $this->parseDate($user->created_at),
                        'updated_at' => $this->parseDate($user->updated_at),
                        'deleted_at' => $this->parseDate($user->deleted_at),
                    ]);

                    $userAddress->save();
                }
            });

            DB::commit();

            $this->info('Migration of user addresses completed.');
        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error('Error during migration: '.$e->getMessage());
        }
    }

    /**
     * Parse date from timestamp or return null if invalid.
     *
     * @param mixed $timestamp
     *
     * @return Carbon|null
     */
    private function parseDate($timestamp)
    {
        return !empty($timestamp) ? Carbon::createFromTimestamp($timestamp) : null;
    }
}
