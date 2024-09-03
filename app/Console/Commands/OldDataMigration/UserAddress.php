<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
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
            $this->info('Migrating old data for users addresses table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('users')->chunkById(1000, function ($users, $key) {
                foreach ($users as $single_user_address) {
                    // Check if the user exists
                    $checkUser = \App\Models\User::find($single_user_address->id);
                    if ($checkUser === null) {
                        continue;
                    }
                    // Check if a UserAddress already exists for the user
                    if (\App\Models\UserAddress::where('user_id', $single_user_address->id)->exists()) {
                        continue;
                    }
                    // Retrieve an existing UserAddress or create a new one
                    $userAddress = \App\Models\UserAddress::firstOrNew(['user_id' => $single_user_address->id]);

                    // Parse date fields with Carbon or set them to null if empty
                    $createdAt = !empty($single_user_address->created_at) ? Carbon::createFromTimestamp($single_user_address->created_at) : null;
                    $updateAt = !empty($single_user_address->updated_at) ? Carbon::createFromTimestamp($single_user_address->updated_at) : null;
                    $deletedAt = !empty($single_user_address->deleted_at) ? Carbon::createFromTimestamp($single_user_address->deleted_at) : null;

                    // Fill the model attributes
                    $userAddress->fill([
                        'user_id'      => $single_user_address->id,
                        'latitude'     => $single_user_address->latitude,
                        'longitude'    => $single_user_address->longitude,
                        'city'         => $single_user_address->city,
                        'address'      => $single_user_address->address,
                        'state'        => $single_user_address->states,
                        'country'      => $single_user_address->country,
                        'created_at'   => $createdAt,
                        'updated_at'   => $updateAt,
                        'deleted_at'   => $deletedAt,
                    ]);
                    // Save the model
                    $userAddress->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users table completed.');
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
