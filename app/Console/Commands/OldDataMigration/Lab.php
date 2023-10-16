<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Category;
use App\Models\Lab as ModelsLab;
use App\Models\Organization;
use App\Models\User;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Lab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:labs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old lab table data to new db structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for labs table.');
            $labs = DB::connection('mysql2')->table('labs')->get();
            if ($labs->count() > 0) {
                foreach ($labs as $lab) {
                    $checkUser = User::find($lab->user_id);
                    if (!$checkUser) {
                        continue;
                    }

                    $checkOrganizatioon = Organization::find($lab->organisation);
                    if (!$checkOrganizatioon) {
                        continue;
                    }

                    $category = '1';
                    if ($lab->category) {
                        $checkOldCategory = DB::connection('mysql2')->table('categories')->find($lab->category);
                        $checkCategory = Category::where('title', $checkOldCategory->name)->first();
                        if ($checkCategory) {
                            $category = $checkCategory->id;
                        }
                    }
                    
                    $checkLab = ModelsLab::find($lab->id);
                    if ($checkLab) {
                        $newLab = $checkLab;
                    } else {
                        $newLab = new Lab();
                    }

                    $privacy = config('constants.lab_privacy.no');
                    switch ($lab->privacy) {
                        case 'yes':
                            $privacy = config('constants.lab_privacy.yes');
                            break;
                        case 'no':
                            $privacy = config('constants.lab_privacy.no');
                            break;
                        default:
                            $privacy = config('constants.lab_privacy.yes');
                            break;
                    }

                    switch ($lab->res_sequence) {
                        case '0':
                            $res_sequencial = '0';
                            break;
                        case '1':
                            $res_sequencial = '1';
                            break;                        
                        default:
                            $res_sequencial = '0';
                            break;
                    }

                    switch ($lab->cha_sequence) {
                        case '0':
                            $cha_sequencial = '0';
                            break;
                        case '1':
                            $cha_sequencial = '1';
                            break;
                        default:
                            $cha_sequencial = '0';
                            break;
                    }

                    switch ($lab->enable_achievement) {
                        case '0':
                            $enable_achievement = '0';
                            break;
                        case '1':
                            $enable_achievement = '1';
                            break;
                        default:
                            $enable_achievement = '0';
                            break;
                    }

                    switch ($lab->verification) {
                        case '0':
                            $lab_verfied = '0';
                            break;
                        case '1':
                            $lab_verfied = '1';
                            break;
                        default:
                            $lab_verfied = '0';
                            break;
                    }

                    $newLab = $lab->id;
                    $newLab->type = '4';
                    $newLab->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newLab->language = $lab->language;
                    $newLab->user_id = $lab->user_id;
                    $newLab->organization_id = $lab->organisation;
                    $newLab->category_id = $category;
                    $newLab->duration_id = '1';
                    $newLab->level_id = '1';
                    $newLab->slug = $lab->slug;
                    $newLab->title = $lab->slug;
                    $newLab->description = $lab->description;
                    $newLab->privacy = $privacy;
                    $newLab->media_type = $lab->privacy;
                    $newLab->media = $lab->image;
                    $newLab->status = '1';
                    $newLab->total_share = $lab->total_share;
                    $newLab->is_auto_created = $lab->total_share;
                    $newLab->is_resource_sequential = $res_sequencial;
                    $newLab->is_sequential = $cha_sequencial;
                    $newLab->is_achievement_enabled = $enable_achievement;
                    $newLab->is_notification_enabled = '0';
                    $newLab->is_verified = $lab_verfied;
                    // $newLab->save();


                }
            }

        } catch (Exception $e) {
            return;
        }
    }
}
