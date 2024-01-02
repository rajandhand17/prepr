<?php

namespace App\Console\Commands\OldDataMigration;

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
    protected $description = 'This command will migrate all users certificates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users certificate table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_certificates')->chunkById(1000, function ($userCertificates) {

                foreach ($userCertificates as $userCertificate) {
                    $checkUsers = \App\Models\User::find($userCertificate->user_id);
                    if ($checkUsers == null) {
                        continue;
                    }
                $userCertificateDetails = \App\Models\UserCertificate::where('id', $userCertificate->id)->first();
                if ($userCertificateDetails) {
                    $certificates = $userCertificateDetails;
                } else {
                    $certificates = new \App\Models\UserCertificate();
                }
                $certificates->user_id = $userCertificate->user_id;
                $certificates->company = $userCertificate->company;
                $certificates->name = $userCertificate->name;
                $certificates->start_date = $userCertificate->start_date;
                $certificates->end_date = $userCertificate->end_date;
                $certificates->description = $userCertificate->description;
                $certificates->save();
            }
            });
            DB::commit();
            $this->info('Migrating of old data for users certificate table completed.');
        } catch(\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
