<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
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
                    $check_ranks = Ranks::where('title', $single_rank->name)->first();
                    if ($check_ranks) {
                        $newRank = $check_ranks;
                    } else {
                        $newRank = new Ranks();
                    }
                    $newRank->id = $single_rank->id;
                    $newRank->title = $single_rank->name;
                    $newRank->fr_CA_title = $single_rank->fr_CA_name;
                    $newRank->description = $single_rank->description;
                    $newRank->fr_CA_description = $single_rank->fr_CA_description;
                    $newRank->image = $single_rank->image;
                    $newRank->category = $single_rank->category;
                    $newRank->point = $single_rank->point;
                    $newRank->no_of_use = $single_rank->no_of_use;
                    $newRank->status = $single_rank->status;
                    $newRank->save();
                }
                DB::commit();
                $this->info('Migrating of old data for ranks table completed.');

                return;
            }
            DB::rollback();
            $this->error('No ranks found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
