<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ChallengeTag as ChallengeTags;

class ChallengeTag extends Command
{
    /** 
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:challange-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old challange tags table data to new db structure.';

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

            $this->info('Migrating old data for challange tags table.');
            DB::beginTransaction();

            $challange_tags = DB::connection('mysql2')->table('challange_tag')->get();

            if($challange_tags->count() > 0){
                foreach ($challange_tags as $key => $single_challange_tags){
                    $challange_tags_details=[
                        'challange_id' => $single_challange_tags->challange_id,
                        'user_id' => $single_challange_tags->user_id,
                        'tag' => $single_challange_tags->tag,
                    
                    ];
                    $check_challange_tags = ChallengeTags::where($challange_tags_details)->first();
                    if(!$check_challange_tags){
                        ChallengeTags::create($challange_tags_details);
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for challange tags table completed.');
                return;
            }
            DB::rollback();
            $this->error('No challange tags found.');

        } catch (\Exception $e) {
           DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
