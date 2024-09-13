<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\SocialLink as Link;
use DB;
use Illuminate\Console\Command;

class SocialLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:social-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old social link table data to new db structure.';

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
            $this->info('Migrating old data for social link table.');
            DB::beginTransaction();

            $social_link = DB::connection('mysql2')->table('social_link')->get();
            if ($social_link->count() > 0) {
                foreach ($social_link as $key => $single_social_link) {
                    $check_social_links = Link::where('title', $single_social_link->link_name)->first();
                    if ($check_social_links) {
                        $newSocialLink = $check_social_links;
                    } else {
                        $newSocialLink = new Link();
                    }

                    $newSocialLink->id = $single_social_link->id;
                    $newSocialLink->title = $single_social_link->link_name;
                    $newSocialLink->icon = $single_social_link->link_icon;
                    $newSocialLink->save();
                }
                DB::commit();
                $this->info('Migrating of old data for social link table completed.');

                return;
            }
            DB::rollback();
            $this->error('No social link found.');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
