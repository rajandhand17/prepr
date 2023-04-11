<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Host as Hosts;

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
            if($hosts->count() > 0){

                foreach ($hosts as $key => $single_host){
                   $hosts_details=[
                        'name' => $single_host->name,
                        'link' => $single_host->link,
                        'image' => $single_host->image,
                    ];
                    $check_hosts = Hosts::where($hosts_details)->first();
                    if(!$check_hosts){
                        Hosts::create($hosts_details);
                    }

                }
                DB::commit();
                $this->info('Migrating of old data for hosts table completed.');
                return;
            }
            DB::rollback();
            $this->error('No hosts found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}
