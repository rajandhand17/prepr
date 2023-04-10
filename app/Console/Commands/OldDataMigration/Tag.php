<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\Tag as Tags;

class Tag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old tag table data to new db structure.';

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
            $this->info('Migrating old data for tags table.');
            DB::beginTransaction();

            $tags = DB::connection('mysql2')->table('tags')->get();
            if($tags->count() > 0){

                foreach ($tags as $key => $single_tags){
                   $tags_details=[
                        'name' => $single_tags->tag,
                        'fr_CA_name' => $single_tags->fr_CA_tag,
                        'tag_image'=>$single_tags->tag_image,
                        'fr_CA_tag_image'=>$single_tags->fr_CA_tag_image,
                        'components'=>$single_tags->category,
                    ];
                    $check_tags = Tags::where($tags_details)->first();
                    if(!$check_tags){
                        Tags::create($tags_details);
                    }

                }
                DB::commit();
                $this->info('Migrating of old data for tags table completed.');
                return;
            }
            DB::rollback();
            $this->error('No tag found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}
