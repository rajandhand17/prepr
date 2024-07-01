<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Tag as Tags;
use DB;
use Illuminate\Console\Command;

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
            if ($tags->count() > 0) {
                foreach ($tags as $key => $single_tags) {
                    $check_tags = Tags::where('title', $single_tags->tag)->first();
                    if ($check_tags) {
                        $newTag = $check_tags;
                    } else {
                        $newTag = new Tags();
                    }
                    $newTag->id = $single_tags->id;
                    $newTag->title = $single_tags->tag;
                    $newTag->fr_CA_title = $single_tags->fr_CA_tag;
                    $newTag->tag_image = $single_tags->tag_image;
                    $newTag->fr_CA_tag_image = $single_tags->fr_CA_tag_image;
                    $newTag->components = $single_tags->category;
                    $newTag->save();
                }
                DB::commit();
                $this->info('Migrating of old data for tags table completed.');

                return;
            }
            DB::rollback();
            $this->error('No tag found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
