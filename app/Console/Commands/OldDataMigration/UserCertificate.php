<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class UserCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users-certificate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will migrate all users achievements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users certificate table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_certificates')->chunkById(1000, function ($userCertificate, $key) {
                foreach ($userCertificate as $single_user_certificate) {
                    // Check if the user exists
                    $checkUser = \App\Models\User::find($single_user_certificate->user_id);
                    if ($checkUser === null) {
                        continue;
                    }
                    // Retrieve an existing UserCertificate or create a new one
                    $userCertificate = \App\Models\UserCertificate::firstOrNew(['id' => $single_user_certificate->id]);

                    // Parse date fields with Carbon or set them to null if empty
                    $createdAt = !empty($single_user_certificate->created_at) ? Carbon::createFromTimestamp($single_user_certificate->created_at) : null;
                    $updateAt = !empty($single_user_certificate->updated_at) ? Carbon::createFromTimestamp($single_user_certificate->updated_at) : null;
                    $deletedAt = !empty($single_user_certificate->deleted_at) ? Carbon::createFromTimestamp($single_user_certificate->deleted_at) : null;

                    // Fill the model attributes
                    $userCertificate->fill([
                        'user_id'     => $single_user_certificate->user_id,
                        'company'     => $single_user_certificate->company,
                        'name'        => $single_user_certificate->name,
                        'start_date'  => $single_user_certificate->start_date,
                        'end_date'    => $single_user_certificate->end_date,
                        'description' => $single_user_certificate->description,
                        'created_at'  => $createdAt,
                        'updated_at'  => $updateAt,
                        'deleted_at'  => $deletedAt,
                    ]);
                    // Save the model
                    $userCertificate->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users  certificate table completed.');
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
