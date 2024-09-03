<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\TagGroup as ModelsTagGroup;
use DB;
use Illuminate\Console\Command;

class TagGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:tag-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old tag groups table data to new db structure.';

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
            $this->info('Migrating old data for tag groups table.');
            DB::beginTransaction();

            $tag_groups = DB::connection('mysql2')->table('tag_groups')->get();
            if ($tag_groups->count() > 0) {
                foreach ($tag_groups as $key => $tag_group) {
                    $tags = [];
                    if ($tag_group->tags != null) {
                        if (str_contains($tag_group->tags, ',')) {
                            $tags = explode(',', $tag_group->tags);
                        } else {
                            $tags = [$tag_group->tags];
                        }
                    }
                    $check_tag_group = ModelsTagGroup::where('title', $tag_group->title)->first();
                    if ($check_tag_group) {
                        $newTagGroup = $check_tag_group;
                    } else {
                        $newTagGroup = new ModelsTagGroup();
                    }
                    $newTagGroup->id = $tag_group->id;
                    $newTagGroup->title = $tag_group->title;
                    $newTagGroup->fr_CA_title = $tag_group->fr_CA_title;
                    $newTagGroup->description = $tag_group->description;
                    $newTagGroup->fr_CA_description = $tag_group->fr_CA_description;
                    $newTagGroup->tags = $tags;
                    $newTagGroup->save();
                }
                DB::commit();
                $this->info('Migrating of old data for tag groups table completed.');

                return;
            }
            DB::rollback();
            $this->error('No tag groups found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
