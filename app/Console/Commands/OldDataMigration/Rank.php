<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Rank as Ranks;
use DB;
use Illuminate\Console\Command;

class Rank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:ranks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old ranks table data to new db structure.';

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
            $this->info('Migrating old data for ranks table.');
            DB::beginTransaction();

            $ranks = DB::connection('mysql2')->table('ranks')->get();
            if ($ranks->count() > 0) {
                foreach ($ranks as $key => $single_rank) {
                    $rank_details = [
                        'title'              => $single_rank->name,
                        'fr_CA_title'        => $single_rank->fr_CA_name,
                        'description'       => $single_rank->description,
                        'fr_CA_description' => $single_rank->fr_CA_description,
                        'image'             => $single_rank->image,
                        'category'          => $single_rank->category,
                        'point'             => $single_rank->point,
                        'no_of_use'         => $single_rank->no_of_use,
                        'status'            => $single_rank->status,
                    ];
                    $check_ranks = Ranks::where($rank_details)->first();
                    if (!$check_ranks) {
                        Ranks::create($rank_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for ranks table completed.');

                return;
            }
            DB::rollback();
            $this->error('No ranks found.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
