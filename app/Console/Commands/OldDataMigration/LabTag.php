<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\LabTag as LabTags;

class LabTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:lab-tag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old lab tag table data to new db structure.';

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

            $this->info('Migrating old data for lab tag table.');
            DB::beginTransaction();

            $lab_tags = DB::connection('mysql2')->table('lab_tag')->get();
            if($lab_tags->count() > 0){
                foreach ($lab_tags as $key => $single_lab_tags){
                   $lab_tags_details=[
                        'user_id' => $single_lab_tags->user_id,
                        'lab_id' => $single_lab_tags->lab_id,
                        'tag' => $single_lab_tags->tag,
                    ];
                    
                    $check_lab_tags = LabTags::where($lab_tags_details)->first();
                    if(!$check_lab_tags){
                        LabTags::create($lab_tags_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for lab tags table completed.');
                return;
            }
            DB::rollback();
            $this->error('No lab tags found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
