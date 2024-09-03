<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Host as Hosts;
use DB;
use Illuminate\Console\Command;

class Host extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:host';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old host table data to new db structure.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for host table.');
            DB::beginTransaction();

            $hosts = DB::connection('mysql2')->table('hosts')->get();
            if ($hosts->count() > 0) {
                foreach ($hosts as $key => $single_host) {
                    $check_hosts = Hosts::where(['title' => $single_host->name, 'link' => $single_host->link])->first();
                    if ($check_hosts) {
                        $newHost = $check_hosts;
                    } else {
                        $newHost = new Hosts();
                    }
                    $newHost->id = $single_host->id;
                    $newHost->title = $single_host->name;
                    $newHost->link = $single_host->link;
                    $newHost->image = $single_host->image;
                    $newHost->save();
                }
                DB::commit();
                $this->info('Migrating of old data for hosts table completed.');

                return;
            }
            DB::rollback();
            $this->error('No hosts found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
