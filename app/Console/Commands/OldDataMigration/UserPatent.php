<?php

namespace App\Console\Commands\OldDataMigration;

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
    protected $description = 'This command will migrate all users patent data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users patent table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('user_patents')->chunkById(1000, function ($userPatents) {

                foreach ($userPatents as $userPatent) {
                    $checkUsers = \App\Models\User::find($userPatent->user_id);
                    if ($checkUsers == null) {
                        continue;
                    }
                    $userPatentExistsOrNot = \App\Models\UserCertificate::where('id', $userPatent->id)->first();
                    if ($userPatentExistsOrNot) {
                        $patent = $userPatentExistsOrNot;
                    } else {
                        $patent = new \App\Models\UserPatent();
                    }
                    $patent->user_id = $userPatent->user_id;
                    $patent->title = $userPatent->title;
                    $patent->name = $userPatent->name;
                    $patent->patent_date = $userPatent->patent_date;
                    $patent->description = $userPatent->description;
                    $patent->save();
                }
            });
            DB::commit();
            $this->info('Migrating of old data for users patents table completed.');
        } catch(\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
